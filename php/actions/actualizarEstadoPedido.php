<?php
require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../dao/PedidoDao.php';
require_once '../includes/alert_helper.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Tu sesión ha expirado', [], 'Sesión Expirada');
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Método no permitido', [], 'Error de Solicitud');
    exit;
}

try {
    // Obtener datos
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Datos no válidos', [], 'Datos Inválidos');
        exit;
    }
    
    $pedidoId = isset($data['pedidoId']) ? (int)$data['pedidoId'] : 0;
    $nuevoEstado = isset($data['estado']) ? trim($data['estado']) : '';
    
    // Validar datos
    $estadosValidos = ['pendiente', 'procesando', 'completado', 'cancelado'];
    if (!$pedidoId || !in_array($nuevoEstado, $estadosValidos)) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Los datos proporcionados no son válidos', [], 'Datos Inválidos');
        exit;
    }
    
    // Verificar que el pedido existe y el usuario tiene acceso
    $pedido = findPedidoById($pedidoId);
    if (!$pedido) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'El pedido solicitado no existe o no tienes permisos para verlo', [], 'Pedido No Encontrado');
        exit;
    }
    
    // Obtener ID de empresa del usuario
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }
    
    // Verificar permisos según el tipo de cambio
    $esProveedor = ($pedido->getIdEmpresaProveedor() == $idEmpresa);
    $esCliente = ($pedido->getIdEmpresaCliente() == $idEmpresa);
    
    // Validar que el usuario tiene relación con el pedido
    if (!$esProveedor && !$esCliente) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No tienes permisos para modificar este pedido', [], 'Sin Permisos');
        exit;
    }
    
    // Validar transiciones de estado permitidas
    $estadoActual = $pedido->getEstado();
    
    // Solo el proveedor puede marcar como procesando o completado
    if (($nuevoEstado == 'procesando' || $nuevoEstado == 'completado') && !$esProveedor) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Solo el proveedor puede marcar el pedido como procesando o completado', [], 'Sin Permisos');
        exit;
    }
    
    // Solo el cliente puede cancelar un pedido pendiente
    if ($nuevoEstado == 'cancelado' && !$esCliente) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Solo el cliente puede cancelar el pedido', [], 'Sin Permisos');
        exit;
    }
    
    // Validar transiciones lógicas de estado
    $transicionesValidas = [
        'pendiente' => ['procesando', 'cancelado'],
        'procesando' => ['completado'],
        'completado' => [], // No se puede cambiar desde completado
        'cancelado' => [] // No se puede cambiar desde cancelado
    ];
    
    if (!in_array($nuevoEstado, $transicionesValidas[$estadoActual])) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, "No es posible cambiar el estado de '{$estadoActual}' a '{$nuevoEstado}'", [], 'Transición Inválida');
        exit;
    }
    
    // Actualizar estado
    $resultado = actualizarEstadoPedido($pedidoId, $nuevoEstado);
    
    if ($resultado) {
        // Mensajes personalizados según el estado
        $mensajes = [
            'procesando' => 'El pedido está ahora siendo procesado. El cliente será notificado del cambio de estado.',
            'completado' => 'El pedido se ha marcado como completado exitosamente. ¡Excelente trabajo!',
            'cancelado' => 'El pedido ha sido cancelado. El proveedor será notificado de la cancelación.'
        ];
        
        $mensaje = $mensajes[$nuevoEstado] ?? 'El estado del pedido se ha actualizado correctamente';
        
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(true, $mensaje, ['nuevoEstado' => $nuevoEstado], 'Estado Actualizado');
    } else {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No se pudo actualizar el estado del pedido. Inténtalo de nuevo.', [], 'Error de Actualización');
    }
    
} catch (Exception $e) {
    error_log("Error en actualizarEstadoPedido.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Se ha producido un error inesperado en el servidor', [], 'Error del Sistema');
}
?>