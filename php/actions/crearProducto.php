<?php
// ===== ARCHIVO 4: Ejemplo de action corregido =====
// php/actions/crearProducto.php (EJEMPLO DE CORRECCIÓN)

// Incluir helper de sesión
require_once '../includes/session_helper.php';
require_once '../model/Producto.php';
require_once '../dao/ProductoDao.php';
require_once '../includes/alert_helper.php';

// Verificar autenticación
if (!verificarAutenticacion()) {
    AlertHelper::error('Tu sesión ha expirado. Por favor, inicia sesión nuevamente', 'Sesión Expirada');
    header('Location: ../view/login.php');
    exit;
}

if (isset($_POST['codigo_seguimiento']) && isset($_POST['nombre_producto']) && isset($_POST['descripcion']) && isset($_POST['id_categoria']) && isset($_POST['precio']) && isset($_POST['iva'])) {
    
    $codigoSeguimiento = trim($_POST['codigo_seguimiento']);
    $nombreProducto = trim($_POST['nombre_producto']);
    $descripcion = trim($_POST['descripcion']);
    $idCategoria = $_POST['id_categoria'];
    $precio = $_POST['precio'];
    $iva = $_POST['iva'];
    $activo = isset($_POST['activo']) ? true : false;
    
    // Validaciones
    if (strlen($nombreProducto) < 2) {
        AlertHelper::error('El nombre del producto debe tener al menos 2 caracteres', 'Datos Inválidos');
        header('Location: ../view/creacionProducto.php');
        exit;
    }
    
    if ($precio <= 0) {
        AlertHelper::error('El precio debe ser mayor a 0', 'Precio Inválido');
        header('Location: ../view/creacionProducto.php');
        exit;
    }
    
    // Verificar si el código ya existe
    if (findByCodigoSeguimiento($codigoSeguimiento)) {
        AlertHelper::warning("El código de seguimiento '{$codigoSeguimiento}' ya existe. Por favor, usa un código diferente.", 'Código Duplicado');
        header('Location: ../view/creacionProducto.php');
        exit;
    }
    
    $ruta_imagen_producto = '';
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['imagen']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            AlertHelper::error('La imagen debe ser JPG, PNG o GIF', 'Formato Inválido');
            header('Location: ../view/creacionProducto.php');
            exit;
        }
        
        if ($_FILES['imagen']['size'] > 2 * 1024 * 1024) { // 2MB
            AlertHelper::error('La imagen no puede ser mayor a 2MB', 'Archivo Muy Grande');
            header('Location: ../view/creacionProducto.php');
            exit;
        }
        
        $nombre_imagen = uniqid() . "_" . basename($_FILES['imagen']['name']);
        $ruta_destino = '../../uploads/imagenesProductos/' . $nombre_imagen;
        
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
            $ruta_imagen_producto = '/uploads/imagenesProductos/' . $nombre_imagen;
        } else {
            AlertHelper::warning('No se pudo subir la imagen, pero el producto se creará sin imagen', 'Imagen No Subida');
        }
    }

    try {
        $idEmpresa = obtenerIdEmpresa();
        $producto = new Producto(null, $codigoSeguimiento, $nombreProducto, $descripcion, $idCategoria, $ruta_imagen_producto, $precio, $iva, $idEmpresa, date('Y-m-d H:i:s'), $activo);
        
        $resultado = crearProducto($producto);
        
        if ($resultado) {
            AlertHelper::success("El producto '{$nombreProducto}' se ha creado correctamente y está disponible en tu catálogo", 'Producto Creado');
        } else {
            AlertHelper::error('Hubo un problema al guardar el producto en la base de datos. Inténtalo de nuevo.', 'Error al Guardar');
        }
        
    } catch (Exception $e) {
        error_log("Error en crearProducto.php: " . $e->getMessage());
        AlertHelper::error('No se pudo crear el producto: ' . $e->getMessage(), 'Error al Crear');
    }
    
    header('Location: ../view/productos.php');
    exit;
    
} else {
    AlertHelper::error('Faltan datos obligatorios para crear el producto', 'Datos Incompletos');
    header('Location: ../view/creacionProducto.php');
    exit;
}