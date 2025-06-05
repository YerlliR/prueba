<?php
// ===== ARCHIVO: php/actions/procesarSolicitud.php (CORREGIDO) =====

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../model/Solicitud.php';
require_once '../dao/SolicitudDao.php';
require_once '../includes/alert_helper.php';

// Configurar headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Función para enviar respuesta JSON y terminar
function sendJsonResponse($success, $message, $data = [], $title = '') {
    echo AlertHelper::jsonResponse($success, $message, $data, $title);
    exit;
}

// Verificar que el usuario está autenticado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    sendJsonResponse(false, 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente', [], 'Sesión Expirada');
}

// Verificar que la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Método de solicitud no válido', [], 'Error de Solicitud');
}

// Log para debug
error_log("Procesando solicitud - Método: " . $_SERVER['REQUEST_METHOD']);
error_log("Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'No definido'));

try {
    // Obtener los datos del cuerpo de la solicitud
    $input = file_get_contents('php://input');
    error_log("Raw input: " . $input);
    
    // Intentar decodificar JSON
    $data = json_decode($input, true);
    
    // Si no es JSON válido, intentar obtener de $_POST
    if (!$data && !empty($_POST)) {
        $data = $_POST;
        error_log("Usando datos de POST: " . print_r($_POST, true));
    }
    
    // Verificar que se recibieron datos
    if (!$data) {
        error_log("No se pudieron obtener datos - Input: " . $input);
        sendJsonResponse(false, 'Datos de solicitud no válidos o vacíos', [], 'Datos Inválidos');
    }
    
    error_log("Datos recibidos: " . print_r($data, true));
    
    // Verificar que se recibieron todos los datos necesarios
    if (!isset($data['id_empresa_proveedor']) || !isset($data['asunto']) || !isset($data['mensaje'])) {
        $missing = [];
        if (!isset($data['id_empresa_proveedor'])) $missing[] = 'id_empresa_proveedor';
        if (!isset($data['asunto'])) $missing[] = 'asunto';
        if (!isset($data['mensaje'])) $missing[] = 'mensaje';
        
        error_log("Faltan campos: " . implode(', ', $missing));
        sendJsonResponse(false, 'Faltan datos obligatorios: ' . implode(', ', $missing), [], 'Datos Incompletos');
    }
    
    // Limpiar y validar datos
    $asunto = trim($data['asunto']);
    $mensaje = trim($data['mensaje']);
    $idEmpresaProveedor = (int)$data['id_empresa_proveedor'];
    
    error_log("Datos procesados - Asunto: $asunto, Mensaje: " . substr($mensaje, 0, 50) . "..., Proveedor ID: $idEmpresaProveedor");
    
    // Validar longitud de los campos
    if (strlen($asunto) < 5) {
        sendJsonResponse(false, 'El asunto debe tener al menos 5 caracteres', [], 'Asunto Muy Corto');
    }
    
    if (strlen($asunto) > 100) {
        sendJsonResponse(false, 'El asunto no puede exceder 100 caracteres', [], 'Asunto Muy Largo');
    }
    
    if (strlen($mensaje) < 20) {
        sendJsonResponse(false, 'El mensaje debe tener al menos 20 caracteres para ser más descriptivo', [], 'Mensaje Muy Corto');
    }
    
    if (strlen($mensaje) > 1000) {
        sendJsonResponse(false, 'El mensaje no puede exceder 1000 caracteres', [], 'Mensaje Muy Largo');
    }
    
    // Validar ID del proveedor
    if ($idEmpresaProveedor <= 0) {
        sendJsonResponse(false, 'ID de proveedor no válido', [], 'Proveedor Inválido');
    }
    
    // Obtener ID de empresa desde la sesión
    $idEmpresaSolicitante = $_SESSION['empresa']['id'];
    if (is_array($idEmpresaSolicitante)) {
        $idEmpresaSolicitante = $idEmpresaSolicitante[0];
    }
    $idEmpresaSolicitante = (int)$idEmpresaSolicitante;
    
    error_log("ID Empresa Solicitante: $idEmpresaSolicitante");
    
    // Verificar que no se esté enviando solicitud a sí mismo
    if ($idEmpresaSolicitante === $idEmpresaProveedor) {
        sendJsonResponse(false, 'No puedes enviarte una solicitud a ti mismo', [], 'Solicitud Inválida');
    }
    
    // Verificar si ya existe una solicitud pendiente a esta empresa
    $solicitudesExistentes = findSolicitudesPendientesEnviadas($idEmpresaSolicitante);
    foreach ($solicitudesExistentes as $solicitudExistente) {
        if ($solicitudExistente->getIdEmpresaProveedor() == $idEmpresaProveedor) {
            sendJsonResponse(false, 'Ya tienes una solicitud pendiente con esta empresa. Espera su respuesta antes de enviar otra.', [], 'Solicitud Duplicada');
        }
    }
    
    // Verificar que la empresa proveedora existe
    require_once '../dao/EmpresaDao.php';
    $empresaProveedor = findById($idEmpresaProveedor);
    if (!$empresaProveedor) {
        sendJsonResponse(false, 'La empresa seleccionada no existe', [], 'Empresa No Encontrada');
    }
    
    error_log("Empresa proveedor encontrada: " . $empresaProveedor['nombre']);
    
    // Crear objeto Solicitud
    $solicitud = new Solicitud(
        null,
        $idEmpresaSolicitante,
        $idEmpresaProveedor,
        $asunto,
        $mensaje,
        'pendiente',
        date('Y-m-d H:i:s')
    );
    
    error_log("Objeto solicitud creado");
    
    // Guardar solicitud en la base de datos
    $resultado = guardarSolicitud($solicitud);
    
    error_log("Resultado de guardar solicitud: " . ($resultado ? 'true' : 'false'));
    
    // Responder al cliente
    if ($resultado) {
        $mensaje = "Tu solicitud '{$asunto}' ha sido enviada correctamente a {$empresaProveedor['nombre']}. El proveedor recibirá una notificación y podrá responder a tu solicitud.";
        
        // Log de éxito
        error_log("Solicitud enviada exitosamente de empresa $idEmpresaSolicitante a empresa $idEmpresaProveedor");
        
        sendJsonResponse(true, $mensaje, [
            'solicitud_id' => $idEmpresaProveedor,
            'empresa_proveedor' => $empresaProveedor['nombre']
        ], 'Solicitud Enviada');
    } else {
        error_log("Error al guardar solicitud en la base de datos");
        sendJsonResponse(false, 'No se pudo enviar la solicitud debido a un error en el servidor', [], 'Error de Envío');
    }
    
} catch (Exception $e) {
    error_log("Excepción en procesarSolicitud.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    sendJsonResponse(false, 'Se ha producido un error inesperado. Inténtalo de nuevo más tarde.', [], 'Error del Sistema');
}
?>