<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../db/conexionDb.php';
require_once '../dao/ProductoDao.php';
require_once '../includes/alert_helper.php';

header('Content-Type: application/json');

if (isset($_POST['idProducto'])) {
    $idProducto = (int)$_POST['idProducto'];
    
    // Validar que el ID es válido
    if ($idProducto <= 0) {
        echo AlertHelper::jsonResponse(false, 'ID de producto no válido', [], 'Datos Inválidos');
        exit;
    }
    
    // Verificar que el usuario tiene una empresa seleccionada
    if (!isset($_SESSION['empresa']['id'])) {
        echo AlertHelper::jsonResponse(false, 'No hay empresa seleccionada', [], 'Sesión Inválida');
        exit;
    }
    
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }
    
    try {
        // Verificar que el producto pertenece a la empresa actual
        if (!findProductoEnEmpresa($idEmpresa, $idProducto)) {
            echo AlertHelper::jsonResponse(false, 'No tienes permisos para eliminar este producto', [], 'Sin Permisos');
            exit;
        }
        
        // Obtener información del producto antes de eliminarlo
        $producto = findProductoById($idProducto);
        $nombreProducto = $producto ? $producto->getNombreProducto() : 'el producto';
        
        $result = eliminarProducto($idProducto);

        if ($result) {
            echo AlertHelper::jsonResponse(true, "'{$nombreProducto}' se ha eliminado correctamente de tu catálogo", [], 'Producto Eliminado');
        } else {
            echo AlertHelper::jsonResponse(false, 'No se pudo eliminar el producto. Es posible que ya haya sido eliminado anteriormente.', [], 'Error al Eliminar');
        }
    } catch (Exception $e) {
        error_log("Error al eliminar producto: " . $e->getMessage());
        echo AlertHelper::jsonResponse(false, 'Error del servidor: ' . $e->getMessage(), [], 'Error del Sistema');
    }
} else {
    echo AlertHelper::jsonResponse(false, 'ID de producto no proporcionado', [], 'Datos Incompletos');
}
?>