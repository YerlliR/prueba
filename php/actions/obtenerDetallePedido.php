<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../model/Pedido.php';
require_once '../dao/PedidoDao.php';
require_once '../dao/EmpresaDao.php';

// Verificar autenticación
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autorizado']);
    exit;
}

// Obtener ID del pedido
$pedidoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$pedidoId) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => 'ID de pedido no válido']);
    exit;
}

try {
    // Obtener pedido con sus líneas
    $pedido = findPedidoById($pedidoId);
    
    if (!$pedido) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'Pedido no encontrado']);
        exit;
    }
    
    // Verificar que el usuario tenga acceso al pedido
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }
    
    if ($pedido->getIdEmpresaCliente() != $idEmpresa && $pedido->getIdEmpresaProveedor() != $idEmpresa) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'No tienes acceso a este pedido']);
        exit;
    }
    
    // Determinar si es pedido recibido o enviado
    $tipo = ($pedido->getIdEmpresaProveedor() == $idEmpresa) ? 'recibido' : 'enviado';
    
    // Obtener información de la empresa
    $idEmpresaInfo = ($tipo == 'recibido') ? $pedido->getIdEmpresaCliente() : $pedido->getIdEmpresaProveedor();
    $empresaInfo = findById($idEmpresaInfo);
    
    // Preparar datos del pedido
    $pedidoData = [
        'id' => $pedido->getId(),
        'numero_pedido' => $pedido->getNumeroPedido(),
        'fecha_pedido' => $pedido->getFechaPedido(),
        'fecha_entrega_estimada' => $pedido->getFechaEntregaEstimada(),
        'estado' => $pedido->getEstado(),
        'subtotal' => $pedido->getSubtotal(),
        'total_iva' => $pedido->getTotalIva(),
        'total' => $pedido->getTotal(),
        'notas' => $pedido->getNotas(),
        'direccion_entrega' => $pedido->getDireccionEntrega(),
        'tipo' => $tipo,
        'nombre_empresa' => $empresaInfo['nombre'],
        'email_empresa' => $empresaInfo['email'],
        'telefono_empresa' => $empresaInfo['telefono'],
        'lineas' => []
    ];
    
    // Agregar líneas del pedido
    foreach ($pedido->getLineas() as $linea) {
        $pedidoData['lineas'][] = [
            'id_producto' => $linea->getIdProducto(),
            'nombre_producto' => $linea->getNombreProducto(),
            'cantidad' => $linea->getCantidad(),
            'precio_unitario' => $linea->getPrecioUnitario(),
            'iva' => $linea->getIva(),
            'subtotal' => $linea->getSubtotal(),
            'total' => $linea->getTotal()
        ];
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'pedido' => $pedidoData
    ]);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error: ' . $e->getMessage()
    ]);
}
?>