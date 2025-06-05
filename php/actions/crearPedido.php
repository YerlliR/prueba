<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../model/Pedido.php';
require_once '../dao/PedidoDao.php';
require_once '../includes/alert_helper.php';

// Verificar autenticación
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente', [], 'Sesión Expirada');
    exit;
}

// Verificar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Método de solicitud no válido', [], 'Error de Solicitud');
    exit;
}

try {
    // Obtener datos del pedido
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar que se recibieron datos JSON válidos
    if (!$data) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Datos de pedido no válidos', [], 'Datos Inválidos');
        exit;
    }
    
    // Validar datos requeridos
    if (!isset($data['idProveedor']) || !isset($data['productos']) || empty($data['productos'])) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Faltan datos obligatorios. Asegúrate de seleccionar un proveedor y al menos un producto', [], 'Datos Incompletos');
        exit;
    }
    
    // Validar que el proveedor es válido
    $idProveedor = (int)$data['idProveedor'];
    if ($idProveedor <= 0) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'ID de proveedor no válido', [], 'Proveedor Inválido');
        exit;
    }
    
    // Obtener ID de empresa cliente desde la sesión
    $idEmpresaCliente = $_SESSION['empresa']['id'];
    if (is_array($idEmpresaCliente)) {
        $idEmpresaCliente = $idEmpresaCliente[0];
    }
    $idEmpresaCliente = (int)$idEmpresaCliente;
    
    // Verificar que no se está haciendo pedido a sí mismo
    if ($idEmpresaCliente === $idProveedor) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No puedes realizar un pedido a tu propia empresa', [], 'Pedido Inválido');
        exit;
    }
    
    // Crear nuevo pedido
    $pedido = new Pedido();
    $pedido->setIdEmpresaCliente($idEmpresaCliente);
    $pedido->setIdEmpresaProveedor($idProveedor);
    $pedido->setFechaEntregaEstimada($data['fechaEntrega'] ?? null);
    $pedido->setNotas($data['notas'] ?? null);
    $pedido->setDireccionEntrega($data['direccionEntrega'] ?? null);
    
    // Agregar líneas de pedido
    $totalProductos = 0;
    $productosValidos = 0;
    
    foreach ($data['productos'] as $prod) {
        // Validar datos del producto
        if (!isset($prod['id']) || !isset($prod['cantidad']) || !isset($prod['precio'])) {
            continue;
        }
        
        $cantidad = (int)$prod['cantidad'];
        $precio = (float)$prod['precio'];
        $iva = (float)($prod['iva'] ?? 0);
        
        if ($cantidad > 0 && $precio > 0) {
            $linea = new PedidoLinea(
                null,
                null,
                (int)$prod['id'],
                $cantidad,
                $precio,
                $iva
            );
            $pedido->agregarLinea($linea);
            $totalProductos += $cantidad;
            $productosValidos++;
        }
    }
    
    // Validar que hay al menos una línea válida
    if ($productosValidos === 0) {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'Debes seleccionar al menos un producto con cantidad mayor a cero', [], 'Pedido Vacío');
        exit;
    }
    
    // Crear pedido en la base de datos
    $pedidoId = crearPedido($pedido);
    
    if ($pedidoId) {
        $numeroPedido = $pedido->getNumeroPedido();
        $mensaje = "Tu pedido #{$numeroPedido} ha sido enviado correctamente. ";
        $mensaje .= "Total de productos: {$totalProductos}. ";
        $mensaje .= "El proveedor recibirá la solicitud y podrá procesarla.";
        
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(true, $mensaje, [
            'pedidoId' => $pedidoId,
            'numeroPedido' => $numeroPedido,
            'totalProductos' => $totalProductos
        ], 'Pedido Enviado');
    } else {
        header('Content-Type: application/json');
        echo AlertHelper::jsonResponse(false, 'No se pudo procesar tu pedido debido a un error en el servidor. Inténtalo de nuevo.', [], 'Error al Procesar');
    }
    
} catch (Exception $e) {
    error_log("Error en crearPedido.php: " . $e->getMessage());
    header('Content-Type: application/json');
    echo AlertHelper::jsonResponse(false, 'Se ha producido un error inesperado. Por favor, inténtalo de nuevo más tarde.', [], 'Error del Sistema');
}
?>