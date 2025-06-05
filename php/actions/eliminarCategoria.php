<?php 
require_once '../dao/categoriaDao.php';
require_once '../includes/alert_helper.php'; // ← NUEVA LÍNEA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (isset($_POST['idCategoria'])) {
    $idCategoria = $_POST['idCategoria'];
    
    try {
        // Verificar que la categoría existe y pertenece a la empresa
        $categoria = findCategoriaById($idCategoria);
        
        if (!$categoria) {
            echo AlertHelper::jsonResponse(false, 'La categoría no existe', [], 'Categoría No Encontrada');
            exit;
        }
        
        $idEmpresa = $_SESSION['empresa']['id'];
        if (is_array($idEmpresa)) {
            $idEmpresa = $idEmpresa[0];
        }
        
        if ($categoria->getEmpresaId() != $idEmpresa) {
            echo AlertHelper::jsonResponse(false, 'No tienes permisos para eliminar esta categoría', [], 'Sin Permisos');
            exit;
        }
        
        eliminarCategoria($idCategoria);
        echo AlertHelper::jsonResponse(true, 'La categoría ha sido eliminada correctamente', [], 'Categoría Eliminada');
        
    } catch (Exception $e) {
        echo AlertHelper::jsonResponse(false, 'Error del servidor: ' . $e->getMessage(), [], 'Error del Sistema');
    }
} else {
    echo AlertHelper::jsonResponse(false, 'ID de categoría no proporcionado', [], 'Datos Incompletos');
}
?>
