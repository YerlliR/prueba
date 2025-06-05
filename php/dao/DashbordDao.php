<?php
if (!defined('RUTA_DB')) {
    include_once '../constantes/constantesRutas.php';
}
include_once RUTA_DB;

/**
 * DAO para obtener datos estadísticos del dashboard
 */

/**
 * Obtiene estadísticas de pedidos recibidos para una empresa
 */
function obtenerEstadisticasPedidosRecibidos($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'procesando' THEN 1 ELSE 0 END) as procesando,
                SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                SUM(CASE WHEN estado IN ('completado') THEN total ELSE 0 END) as ingresos_totales
            FROM pedidos 
            WHERE id_empresa_proveedor = :id_empresa
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de pedidos recibidos: " . $e->getMessage());
        return [
            'total' => 0,
            'pendientes' => 0,
            'procesando' => 0,
            'completados' => 0,
            'cancelados' => 0,
            'ingresos_totales' => 0
        ];
    }
}

/**
 * Obtiene estadísticas de pedidos enviados para una empresa
 */
function obtenerEstadisticasPedidosEnviados($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'procesando' THEN 1 ELSE 0 END) as en_proceso,
                SUM(CASE WHEN estado = 'completado' THEN 1 ELSE 0 END) as completados,
                SUM(CASE WHEN estado = 'cancelado' THEN 1 ELSE 0 END) as cancelados,
                SUM(CASE WHEN estado IN ('completado') THEN total ELSE 0 END) as gastos_totales
            FROM pedidos 
            WHERE id_empresa_cliente = :id_empresa
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de pedidos enviados: " . $e->getMessage());
        return [
            'total' => 0,
            'pendientes' => 0,
            'en_proceso' => 0,
            'completados' => 0,
            'cancelados' => 0,
            'gastos_totales' => 0
        ];
    }
}

/**
 * Obtiene los pedidos recibidos recientes (últimos 10)
 */
function obtenerPedidosRecibidosRecientes($idEmpresa, $limite = 10) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                p.id,
                p.numero_pedido,
                p.fecha_pedido,
                p.total,
                p.estado,
                e.nombre as nombre_cliente
            FROM pedidos p
            JOIN empresas e ON p.id_empresa_cliente = e.id
            WHERE p.id_empresa_proveedor = :id_empresa
            ORDER BY p.fecha_pedido DESC
            LIMIT :limite
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $pedidos;
    } catch (Exception $e) {
        error_log("Error al obtener pedidos recibidos recientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene los pedidos enviados recientes (últimos 10)
 */
function obtenerPedidosEnviadosRecientes($idEmpresa, $limite = 10) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                p.id,
                p.numero_pedido,
                p.fecha_pedido,
                p.total,
                p.estado,
                e.nombre as nombre_proveedor
            FROM pedidos p
            JOIN empresas e ON p.id_empresa_proveedor = e.id
            WHERE p.id_empresa_cliente = :id_empresa
            ORDER BY p.fecha_pedido DESC
            LIMIT :limite
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $pedidos;
    } catch (Exception $e) {
        error_log("Error al obtener pedidos enviados recientes: " . $e->getMessage());
        return [];
    }
}

/**
 * Obtiene estadísticas de productos para una empresa
 */
function obtenerEstadisticasProductos($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                COUNT(*) as total_productos,
                SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as productos_activos,
                SUM(CASE WHEN activo = 0 THEN 1 ELSE 0 END) as productos_inactivos,
                COUNT(DISTINCT id_categoria) as total_categorias
            FROM productos 
            WHERE id_empresa = :id_empresa AND eliminado = 0
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de productos: " . $e->getMessage());
        return [
            'total_productos' => 0,
            'productos_activos' => 0,
            'productos_inactivos' => 0,
            'total_categorias' => 0
        ];
    }
}

/**
 * Obtiene estadísticas de solicitudes para una empresa
 */
function obtenerEstadisticasSolicitudes($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                SUM(CASE WHEN id_empresa_solicitante = :id_empresa THEN 1 ELSE 0 END) as enviadas,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa THEN 1 ELSE 0 END) as recibidas,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa AND estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes_respuesta
            FROM solicitudes 
            WHERE (id_empresa_solicitante = :id_empresa OR id_empresa_proveedor = :id_empresa)
            AND estado = 'pendiente'
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de solicitudes: " . $e->getMessage());
        return [
            'enviadas' => 0,
            'recibidas' => 0,
            'pendientes_respuesta' => 0
        ];
    }
}

/**
 * Obtiene estadísticas de relaciones comerciales
 */
function obtenerEstadisticasRelaciones($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                SUM(CASE WHEN id_empresa_cliente = :id_empresa THEN 1 ELSE 0 END) as proveedores,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa THEN 1 ELSE 0 END) as clientes
            FROM relaciones_empresa 
            WHERE (id_empresa_cliente = :id_empresa OR id_empresa_proveedor = :id_empresa)
            AND estado = 'activa'
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de relaciones: " . $e->getMessage());
        return [
            'proveedores' => 0,
            'clientes' => 0
        ];
    }
}

/**
 * Obtiene datos completos del dashboard para una empresa
 */
function obtenerDatosDashboard($idEmpresa) {
    return [
        'pedidos_recibidos' => obtenerEstadisticasPedidosRecibidos($idEmpresa),
        'pedidos_enviados' => obtenerEstadisticasPedidosEnviados($idEmpresa),
        'productos' => obtenerEstadisticasProductos($idEmpresa),
        'solicitudes' => obtenerEstadisticasSolicitudes($idEmpresa),
        'relaciones' => obtenerEstadisticasRelaciones($idEmpresa),
        'pedidos_recibidos_recientes' => obtenerPedidosRecibidosRecientes($idEmpresa, 5),
        'pedidos_enviados_recientes' => obtenerPedidosEnviadosRecientes($idEmpresa, 5)
    ];
}

/**
 * Obtiene datos de facturación mensual para gráficos
 */
function obtenerDatosFacturacionMensual($idEmpresa, $meses = 12) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                DATE_FORMAT(fecha_pedido, '%Y-%m') as mes,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa AND estado = 'completado' THEN total ELSE 0 END) as ingresos,
                SUM(CASE WHEN id_empresa_cliente = :id_empresa AND estado = 'completado' THEN total ELSE 0 END) as gastos,
                COUNT(CASE WHEN id_empresa_proveedor = :id_empresa THEN 1 END) as pedidos_recibidos,
                COUNT(CASE WHEN id_empresa_cliente = :id_empresa THEN 1 END) as pedidos_enviados
            FROM pedidos 
            WHERE (id_empresa_proveedor = :id_empresa OR id_empresa_cliente = :id_empresa)
            AND fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL :meses MONTH)
            GROUP BY DATE_FORMAT(fecha_pedido, '%Y-%m')
            ORDER BY mes DESC
        ");
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->bindParam(':meses', $meses, PDO::PARAM_INT);
        $stmt->execute();
        
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $datos;
    } catch (Exception $e) {
        error_log("Error al obtener datos de facturación mensual: " . $e->getMessage());
        return [];
    }
}
?>