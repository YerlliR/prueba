<?php 
require_once '../dao/categoriaDao.php';
require_once '../includes/alert_helper.php'; // ← NUEVA LÍNEA
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $color = $_POST['color'] ?? '#3b82f6';
    
    // Validaciones
    if (strlen($nombre) < 2) {
        AlertHelper::error('El nombre de la categoría debe tener al menos 2 caracteres', 'Nombre Muy Corto');
        header('Location: ../view/productos.php');
        exit;
    }
    
    if (strlen($nombre) > 50) {
        AlertHelper::error('El nombre de la categoría no puede exceder 50 caracteres', 'Nombre Muy Largo');
        header('Location: ../view/productos.php');
        exit;
    }
    
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }

    try {
        $categoria = new Categoria(null, $nombre, $descripcion, $color, $idEmpresa, date('Y-m-d H:i:s'));
        
        guardarCategoria($categoria);
        
        AlertHelper::success("La categoría '{$nombre}' se ha creado correctamente y está disponible para tus productos", 'Categoría Creada');
        
    } catch (Exception $e) {
        AlertHelper::error('No se pudo crear la categoría: ' . $e->getMessage(), 'Error al Crear');
    }
    
    header('Location: ../view/productos.php');
    exit;
    
} else {
    AlertHelper::error('El nombre de la categoría es obligatorio', 'Datos Incompletos');
    header('Location: ../view/productos.php');
    exit;
}
?>