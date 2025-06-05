<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../dao/RelacionesEmpresaDao.php';
require_once '../includes/alert_helper.php';

// Verificar que el usuario está autenticado
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente', [], 'Sesión Expirada');
    exit;
}

// Verificar que la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Método de solicitud no válido', [], 'Método Incorrecto');
    exit;
}

// Obtener el ID de la relación desde POST o JSON
$relacionId = null;

// Primero intentar obtener desde form-data
if (isset($_POST['relacionId'])) {
    $relacionId = (int)$_POST['relacionId'];
} else {
    // Si no está en POST, intentar desde JSON
    $input = file_get_contents('php://input');
    if ($input) {
        $data = json_decode($input, true);
        if (isset($data['relacionId'])) {
            $relacionId = (int)$data['relacionId'];
        }
    }
}

// Validar input
if (!$relacionId || $relacionId <= 0) {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'ID de relación no válido o faltante', [], 'Datos Inválidos');
    exit;
}

try {
    // Obtener ID de la empresa actual
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }
    
    // Verificar que la relación existe y pertenece a la empresa actual
    $db = new conexionDb();
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT * FROM relaciones_empresa WHERE id = :id AND (id_empresa_cliente = :empresa OR id_empresa_proveedor = :empresa)");
    $stmt->bindParam(':id', $relacionId);
    $stmt->bindParam(':empresa', $idEmpresa);
    $stmt->execute();
    
    $relacion = $stmt->fetch(PDO::FETCH_ASSOC);
    $db->closeConnection();
    
    if (!$relacion) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No tienes permisos para eliminar esta relación o la relación no existe', [], 'Sin Permisos');
        exit;
    }
    
    // Terminar la relación
    $resultado = terminarRelacionEmpresa($relacionId);
    
    // Enviar respuesta
    header('Content-Type: application/json');
    if ($resultado) {
        echo AlertHelper::jsonResponse(true, 'La relación comercial se ha terminado correctamente. El proveedor ya no aparecerá en tu lista.', [], 'Relación Terminada');
    } else {
        echo AlertHelper::jsonResponse(false, 'No se pudo terminar la relación. Es posible que ya haya sido eliminada anteriormente.', [], 'Error al Terminar');
    }
} catch (Exception $e) {
    error_log("Error en terminarRelacion.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Error del sistema. Inténtalo de nuevo más tarde.', [], 'Error del Sistema');
}
?>