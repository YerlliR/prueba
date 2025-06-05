<?php
// Archivo: php/actions/obtenerSolicitudes.php

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../dao/SolicitudDao.php';

// Check if user is logged in and has a selected company
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autorizado']);
    exit;
}

// Get the company ID from the session
$idEmpresa = $_SESSION['empresa']['id'];
if (is_array($idEmpresa)) {
    $idEmpresa = $idEmpresa[0]; // Take the first element if it's an array
}
$idEmpresa = (int)$idEmpresa;

// Get the solicitudes type (enviadas or recibidas)
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'enviadas';

try {
    // Get the solicitudes based on the type - ONLY PENDING
    if ($tipo === 'enviadas') {
        $solicitudes = findSolicitudesPendientesEnviadas($idEmpresa);
    } else {
        $solicitudes = findSolicitudesPendientesRecibidas($idEmpresa);
    }
    
    // Convert Solicitud objects to arrays for JSON encoding
    $solicitudesArray = [];
    foreach ($solicitudes as $solicitud) {
        // Get company names
        $empresaSolicitante = getEmpresaNombre($solicitud->getIdEmpresaSolicitante());
        $empresaProveedor = getEmpresaNombre($solicitud->getIdEmpresaProveedor());
        
        $solicitudesArray[] = [
            'id' => $solicitud->getId(),
            'id_empresa_solicitante' => $solicitud->getIdEmpresaSolicitante(),
            'id_empresa_proveedor' => $solicitud->getIdEmpresaProveedor(),
            'empresa_solicitante' => $empresaSolicitante,
            'empresa_proveedor' => $empresaProveedor,
            'asunto' => $solicitud->getAsunto(),
            'mensaje' => $solicitud->getMensaje(),
            'estado' => $solicitud->getEstado(),
            'fecha_creacion' => $solicitud->getFechaCreacion()
        ];
    }
    
    // Return the solicitudes as JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'solicitudes' => $solicitudesArray
    ]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}

// Helper function to get company name by ID
function getEmpresaNombre($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT nombre FROM empresas WHERE id = :id_empresa");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $result ? $result['nombre'] : 'Empresa desconocida';
    } catch (Exception $e) {
        return 'Empresa desconocida';
    }
}
?>