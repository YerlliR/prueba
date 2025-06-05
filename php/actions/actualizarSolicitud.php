<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../dao/SolicitudDao.php';
require_once '../dao/RelacionesEmpresaDao.php';
require_once '../includes/alert_helper.php';

// Check if user is logged in and has a selected company
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente', [], 'Sesión Expirada');
    exit;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Método no permitido', [], 'Error de Solicitud');
    exit;
}

// Get the solicitud ID and action
$solicitudId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$accion = isset($_POST['accion']) ? trim($_POST['accion']) : '';

// Validate inputs
if (!$solicitudId || !in_array($accion, ['aceptar', 'rechazar'])) {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Los datos proporcionados no son válidos', [], 'Datos Inválidos');
    exit;
}

try {
    // Get the solicitud details before updating
    $solicitud = findSolicitudById($solicitudId);
    
    if (!$solicitud) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'La solicitud no existe o ya ha sido procesada', [], 'Solicitud No Encontrada');
        exit;
    }
    
    // Verificar que la solicitud está pendiente
    if ($solicitud->getEstado() !== 'pendiente') {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Esta solicitud ya ha sido procesada anteriormente', [], 'Solicitud Ya Procesada');
        exit;
    }
    
    // Verificar que el usuario actual es el proveedor de la solicitud
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }
    
    if ($solicitud->getIdEmpresaProveedor() != $idEmpresa) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No tienes permisos para procesar esta solicitud', [], 'Sin Permisos');
        exit;
    }
    
    // Map action to estado
    $estado = ($accion === 'aceptar') ? 'aceptada' : 'rechazada';
    
    // Update the solicitud estado
    $resultado = actualizarEstadoSolicitud($solicitudId, $estado);
    
    if (!$resultado) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No se pudo actualizar el estado de la solicitud', [], 'Error de Actualización');
        exit;
    }
    
    // If the solicitud was accepted, create a client-provider relationship
    if ($accion === 'aceptar') {
        $idCliente = $solicitud->getIdEmpresaSolicitante();
        $idProveedor = $solicitud->getIdEmpresaProveedor();
        
        // Create the relationship
        $relacionCreada = crearRelacionEmpresa($idCliente, $idProveedor, $solicitudId);
        
        if ($relacionCreada) {
            $mensaje = 'Solicitud aceptada correctamente. Se ha establecido una nueva relación comercial.';
            $titulo = 'Relación Establecida';
        } else {
            $mensaje = 'Solicitud aceptada, pero hubo un problema al crear la relación comercial.';
            $titulo = 'Solicitud Aceptada';
        }
    } else {
        $mensaje = 'Solicitud rechazada correctamente. El solicitante será notificado.';
        $titulo = 'Solicitud Rechazada';
    }
    
    // Return the result
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(true, $mensaje, [
        'accion' => $accion,
        'estado' => $estado,
        'solicitudId' => $solicitudId
    ], $titulo);
    
} catch (Exception $e) {
    error_log("Error en actualizarSolicitud.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Se ha producido un error inesperado en el servidor', [], 'Error del Sistema');
}
?>