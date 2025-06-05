<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../dao/PedidoDao.php';

// Verificar autenticación
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autorizado']);
    exit;
}

// Obtener ID del proveedor
$idProveedor = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$idProveedor) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => 'ID de proveedor no válido']);
    exit;
}

try {
    // Obtener productos del proveedor
    $productos = obtenerProductosProveedor($idProveedor);
    
    // Agrupar productos por categoría
    $productosPorCategoria = [];
    foreach ($productos as $producto) {
        $categoria = $producto['nombre_categoria'] ?: 'Sin categoría';
        if (!isset($productosPorCategoria[$categoria])) {
            $productosPorCategoria[$categoria] = [
                'color' => $producto['color_categoria'] ?: '#94a3b8',
                'productos' => []
            ];
        }
        $productosPorCategoria[$categoria]['productos'][] = $producto;
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'productos' => $productosPorCategoria
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error al obtener productos: ' . $e->getMessage()
    ]);
}
?>