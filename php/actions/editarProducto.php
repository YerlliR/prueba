<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    require_once '../model/Producto.php';
require_once '../dao/ProductoDao.php';
require_once '../includes/alert_helper.php'; // ← NUEVA LÍNEA

if (isset($_POST['id']) && isset($_POST['codigo_seguimiento']) && isset($_POST['nombre_producto']) && isset($_POST['descripcion']) && isset($_POST['id_categoria']) && isset($_POST['precio']) && isset($_POST['iva'])) {
    $id = $_POST['id']; 
    $codigoSeguimiento = $_POST['codigo_seguimiento'];
    $nombreProducto = $_POST['nombre_producto'];
    $descripcion = $_POST['descripcion'];
    $idCategoria = $_POST['id_categoria'];
    $precio = $_POST['precio'];
    $iva = $_POST['iva'];
    $activo = $_POST['activo'];

    // Validaciones
    if (strlen(trim($nombreProducto)) < 2) {
        AlertHelper::error('El nombre del producto debe tener al menos 2 caracteres', 'Datos Inválidos');
        header('Location: ../view/edicionProducto.php?error=1&id=' . $id);
        exit;
    }

    if ($precio <= 0) {
        AlertHelper::error('El precio debe ser mayor a 0', 'Precio Inválido');
        header('Location: ../view/edicionProducto.php?error=1&id=' . $id);
        exit;
    }

    $ruta_imagen_producto = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $nombre_imagen = uniqid() . "_" . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../uploads/imagenesProductos/' . $nombre_imagen;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $ruta_imagen_producto = '/uploads/imagenesProductos/' . $nombre_imagen;
        } else {
            AlertHelper::error('No se pudo actualizar la imagen del producto', 'Error de Archivo');
            header('Location: ../view/edicionProducto.php?error=1&id=' . $id);
            exit;
        }
    }

    $idEmpresa = (string) $_SESSION['empresa']['id'];

    $producto = new Producto($id, $codigoSeguimiento, $nombreProducto, $descripcion, $idCategoria, $ruta_imagen_producto, $precio, $iva, $idEmpresa[0], date('Y-m-d H:i:s'), $activo);

    try {
        $resultado = editarProducto($producto);
        
        if ($resultado) {
            AlertHelper::success("El producto '{$nombreProducto}' se ha actualizado correctamente", 'Producto Actualizado');
            header('Location: ../view/productos.php');
        } else {
            AlertHelper::error('No se pudieron guardar los cambios. Inténtalo de nuevo.', 'Error al Actualizar');
            header('Location: ../view/edicionProducto.php?error=1&id=' . $id);
        }
    } catch (Exception $e) {
        AlertHelper::error('Error del sistema: ' . $e->getMessage(), 'Error Crítico');
        header('Location: ../view/edicionProducto.php?error=1&id=' . $id);
    }
    
    exit;
} else {
    AlertHelper::error('Faltan datos obligatorios para actualizar el producto', 'Datos Incompletos');
    header('Location: ../view/productos.php');
    exit;
}
?>