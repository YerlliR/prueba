<?php
if (!defined('RUTA_DB')) {
    include_once '../constantes/constantesRutas.php';
}
include_once RUTA_DB;
include_once '../model/Pedido.php';

function generarNumeroPedido() {
    // Generar número único de pedido: AÑO-MES-RANDOM
    return date('Y') . date('m') . '-' . strtoupper(substr(uniqid(), -6));
}

function crearPedido($pedido) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        // Iniciar transacción
        $conn->beginTransaction();
        
        // Generar número de pedido si no existe
        if (!$pedido->getNumeroPedido()) {
            $pedido->setNumeroPedido(generarNumeroPedido());
        }
        
        // Calcular totales
        $pedido->calcularTotales();
        
        // Insertar pedido
        $stmt = $conn->prepare("INSERT INTO pedidos (
            id_empresa_cliente, id_empresa_proveedor, numero_pedido, 
            fecha_pedido, fecha_entrega_estimada, estado, 
            subtotal, total_iva, total, notas, direccion_entrega
        ) VALUES (
            :id_empresa_cliente, :id_empresa_proveedor, :numero_pedido,
            :fecha_pedido, :fecha_entrega_estimada, :estado,
            :subtotal, :total_iva, :total, :notas, :direccion_entrega
        )");
        
        $stmt->execute([
            ':id_empresa_cliente' => $pedido->getIdEmpresaCliente(),
            ':id_empresa_proveedor' => $pedido->getIdEmpresaProveedor(),
            ':numero_pedido' => $pedido->getNumeroPedido(),
            ':fecha_pedido' => $pedido->getFechaPedido(),
            ':fecha_entrega_estimada' => $pedido->getFechaEntregaEstimada(),
            ':estado' => $pedido->getEstado(),
            ':subtotal' => $pedido->getSubtotal(),
            ':total_iva' => $pedido->getTotalIva(),
            ':total' => $pedido->getTotal(),
            ':notas' => $pedido->getNotas(),
            ':direccion_entrega' => $pedido->getDireccionEntrega()
        ]);
        
        $pedidoId = $conn->lastInsertId();
        
        // Insertar líneas de pedido
        $stmtLinea = $conn->prepare("INSERT INTO pedidos_lineas (
            id_pedido, id_producto, cantidad, precio_unitario, 
            iva, subtotal, total
        ) VALUES (
            :id_pedido, :id_producto, :cantidad, :precio_unitario,
            :iva, :subtotal, :total
        )");
        
        foreach ($pedido->getLineas() as $linea) {
            $stmtLinea->execute([
                ':id_pedido' => $pedidoId,
                ':id_producto' => $linea->getIdProducto(),
                ':cantidad' => $linea->getCantidad(),
                ':precio_unitario' => $linea->getPrecioUnitario(),
                ':iva' => $linea->getIva(),
                ':subtotal' => $linea->getSubtotal(),
                ':total' => $linea->getTotal()
            ]);
        }
        
        // Confirmar transacción
        $conn->commit();
        
        $db->closeConnection();
        return $pedidoId;
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollBack();
        }
        error_log("Error al crear pedido: " . $e->getMessage());
        return false;
    }
}

function findPedidoById($id) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        // Obtener pedido
        $stmt = $conn->prepare("SELECT * FROM pedidos WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        
        // Crear objeto pedido
        $pedido = new Pedido(
            $row['id'],
            $row['id_empresa_cliente'],
            $row['id_empresa_proveedor'],
            $row['numero_pedido'],
            $row['fecha_pedido'],
            $row['fecha_entrega_estimada'],
            $row['estado'],
            $row['subtotal'],
            $row['total_iva'],
            $row['total'],
            $row['notas'],
            $row['direccion_entrega']
        );
        
        // Obtener líneas del pedido
        $stmtLineas = $conn->prepare("
            SELECT pl.*, p.nombre_producto 
            FROM pedidos_lineas pl
            JOIN productos p ON pl.id_producto = p.id
            WHERE pl.id_pedido = :id_pedido
        ");
        $stmtLineas->bindParam(':id_pedido', $id);
        $stmtLineas->execute();
        
        while ($linea = $stmtLineas->fetch(PDO::FETCH_ASSOC)) {
            $pedidoLinea = new PedidoLinea(
                $linea['id'],
                $linea['id_pedido'],
                $linea['id_producto'],
                $linea['cantidad'],
                $linea['precio_unitario'],
                $linea['iva'],
                $linea['nombre_producto']
            );
            $pedido->agregarLinea($pedidoLinea);
        }
        
        $db->closeConnection();
        return $pedido;
        
    } catch (Exception $e) {
        error_log("Error al buscar pedido: " . $e->getMessage());
        return null;
    }
}

function findPedidosRecibidos($idEmpresaProveedor) {
    $pedidos = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT p.*, ec.nombre as nombre_cliente
            FROM pedidos p
            JOIN empresas ec ON p.id_empresa_cliente = ec.id
            WHERE p.id_empresa_proveedor = :id_proveedor
            ORDER BY p.fecha_pedido DESC
        ");
        $stmt->bindParam(':id_proveedor', $idEmpresaProveedor);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedido = new Pedido(
                $row['id'],
                $row['id_empresa_cliente'],
                $row['id_empresa_proveedor'],
                $row['numero_pedido'],
                $row['fecha_pedido'],
                $row['fecha_entrega_estimada'],
                $row['estado'],
                $row['subtotal'],
                $row['total_iva'],
                $row['total'],
                $row['notas'],
                $row['direccion_entrega']
            );
            
            // Agregar información adicional como propiedad pública
            $pedido->nombreCliente = $row['nombre_cliente'];
            $pedido->tipo = 'recibido'; // Identificar tipo de pedido
            
            $pedidos[] = $pedido;
        }
        
        $db->closeConnection();
    } catch (Exception $e) {
        error_log("Error al buscar pedidos recibidos: " . $e->getMessage());
    }
    
    return $pedidos;
}

function findPedidosEnviados($idEmpresaCliente) {
    $pedidos = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT p.*, ep.nombre as nombre_proveedor
            FROM pedidos p
            JOIN empresas ep ON p.id_empresa_proveedor = ep.id
            WHERE p.id_empresa_cliente = :id_cliente
            ORDER BY p.fecha_pedido DESC
        ");
        $stmt->bindParam(':id_cliente', $idEmpresaCliente);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pedido = new Pedido(
                $row['id'],
                $row['id_empresa_cliente'],
                $row['id_empresa_proveedor'],
                $row['numero_pedido'],
                $row['fecha_pedido'],
                $row['fecha_entrega_estimada'],
                $row['estado'],
                $row['subtotal'],
                $row['total_iva'],
                $row['total'],
                $row['notas'],
                $row['direccion_entrega']
            );
            
            // Agregar información adicional como propiedad pública
            $pedido->nombreProveedor = $row['nombre_proveedor'];
            $pedido->tipo = 'enviado'; // Identificar tipo de pedido
            
            $pedidos[] = $pedido;
        }
        
        $db->closeConnection();
    } catch (Exception $e) {
        error_log("Error al buscar pedidos enviados: " . $e->getMessage());
    }
    
    return $pedidos;
}

function actualizarEstadoPedido($idPedido, $nuevoEstado) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("UPDATE pedidos SET estado = :estado WHERE id = :id");
        $resultado = $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id' => $idPedido
        ]);
        
        $db->closeConnection();
        return $resultado;
        
    } catch (Exception $e) {
        error_log("Error al actualizar estado del pedido: " . $e->getMessage());
        return false;
    }
}

function obtenerProductosProveedor($idProveedor) {
    $productos = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT p.*, c.nombre_categoria, c.color_categoria
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id
            WHERE p.id_empresa = :id_empresa 
            AND p.activo = 1 
            AND p.eliminado = 0
            ORDER BY c.nombre_categoria, p.nombre_producto
        ");
        $stmt->bindParam(':id_empresa', $idProveedor);
        $stmt->execute();
        
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $db->closeConnection();
    } catch (Exception $e) {
        error_log("Error al obtener productos del proveedor: " . $e->getMessage());
    }
    
    return $productos;
}
?>