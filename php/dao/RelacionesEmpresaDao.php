<?php

if (!defined('RUTA_DB')) {
    include_once '../constantes/constantesRutas.php';
}
include_once RUTA_DB;

function crearRelacionEmpresa($idCliente, $idProveedor, $solicitudId) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        // Comprobar si ya existe la relación
        $stmt = $conn->prepare("SELECT id FROM relaciones_empresa WHERE id_empresa_cliente = :id_cliente AND id_empresa_proveedor = :id_proveedor");
        $stmt->bindParam(':id_cliente', $idCliente);
        $stmt->bindParam(':id_proveedor', $idProveedor);
        $stmt->execute();
        
        $existeRelacion = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existeRelacion) {
            // La relación ya existe, actualizamos el estado
            $stmt = $conn->prepare("UPDATE relaciones_empresa SET estado = 'activa', fecha_inicio = NOW() WHERE id = :id");
            $stmt->bindParam(':id', $existeRelacion['id']);
            $resultado = $stmt->execute();
        } else {
            // Crear una nueva relación
            $stmt = $conn->prepare("INSERT INTO relaciones_empresa (id_empresa_cliente, id_empresa_proveedor, solicitud_id) VALUES (:id_cliente, :id_proveedor, :solicitud_id)");
            $stmt->bindParam(':id_cliente', $idCliente);
            $stmt->bindParam(':id_proveedor', $idProveedor);
            $stmt->bindParam(':solicitud_id', $solicitudId);
            $resultado = $stmt->execute();
        }
        
        $db->closeConnection();
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al crear relación empresa: " . $e->getMessage());
        return false;
    }
}

function obtenerClientesDeProveedor($idProveedor) {
    $clientes = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $sql = "SELECT e.*, r.fecha_inicio, r.id as relacion_id 
                FROM empresas e 
                INNER JOIN relaciones_empresa r ON e.id = r.id_empresa_cliente 
                WHERE r.id_empresa_proveedor = :id_proveedor 
                AND r.estado = 'activa'
                ORDER BY r.fecha_inicio DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_proveedor', $idProveedor);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $clientes[] = $row;
        }
        
        $db->closeConnection();
    } catch (Exception $e) {
        error_log("Error al obtener clientes: " . $e->getMessage());
    }
    
    return $clientes;
}

function obtenerProveedoresDeCliente($idCliente) {
    $proveedores = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $sql = "SELECT e.*, r.fecha_inicio, r.id as relacion_id 
                FROM empresas e 
                INNER JOIN relaciones_empresa r ON e.id = r.id_empresa_proveedor 
                WHERE r.id_empresa_cliente = :id_cliente 
                AND r.estado = 'activa'
                ORDER BY r.fecha_inicio DESC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cliente', $idCliente);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $proveedores[] = $row;
        }
        
        $db->closeConnection();
    } catch (Exception $e) {
        error_log("Error al obtener proveedores: " . $e->getMessage());
    }
    
    return $proveedores;
}

function terminarRelacionEmpresa($relacionId) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        // Primero verificar que la relación existe
        $stmt = $conn->prepare("SELECT id FROM relaciones_empresa WHERE id = :id AND estado = 'activa'");
        $stmt->bindParam(':id', $relacionId);
        $stmt->execute();
        
        if (!$stmt->fetch()) {
            $db->closeConnection();
            return false; // La relación no existe o ya está terminada
        }
        
        // Actualizar el estado a 'terminada'
        $stmt = $conn->prepare("UPDATE relaciones_empresa SET estado = 'terminada', fecha_fin = NOW() WHERE id = :id");
        $stmt->bindParam(':id', $relacionId);
        $resultado = $stmt->execute();
        
        $db->closeConnection();
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al terminar relación: " . $e->getMessage());
        return false;
    }
}

// Función corregida para obtener proveedores de un cliente
function obtenerProveedoresDeProveedor($idCliente) {
    $proveedores = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $sql = "SELECT e.*, r.fecha_inicio, r.id as relacion_id 
                FROM empresas e 
                INNER JOIN relaciones_empresa r ON e.id = r.id_empresa_proveedor 
                WHERE r.id_empresa_cliente = :id_cliente 
                AND r.estado = 'activa'
                ORDER BY r.fecha_inicio DESC"; 
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_cliente', $idCliente);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $proveedores[] = $row;
        }
        
        $db->closeConnection();
    } catch (Exception $e) {
        error_log("Error al obtener proveedores: " . $e->getMessage());
    }
    
    return $proveedores;
}

// Función para verificar si existe una relación activa entre dos empresas
function existeRelacionActiva($idEmpresa1, $idEmpresa2) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $sql = "SELECT COUNT(*) as total FROM relaciones_empresa 
                WHERE ((id_empresa_cliente = :id1 AND id_empresa_proveedor = :id2) 
                    OR (id_empresa_cliente = :id2 AND id_empresa_proveedor = :id1))
                AND estado = 'activa'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id1', $idEmpresa1);
        $stmt->bindParam(':id2', $idEmpresa2);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado['total'] > 0;
    } catch (Exception $e) {
        error_log("Error al verificar relación: " . $e->getMessage());
        return false;
    }
}

// Función para obtener detalles de una relación específica
function obtenerDetalleRelacion($relacionId) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $sql = "SELECT r.*, 
                       ec.nombre as nombre_cliente, ec.email as email_cliente,
                       ep.nombre as nombre_proveedor, ep.email as email_proveedor
                FROM relaciones_empresa r
                INNER JOIN empresas ec ON r.id_empresa_cliente = ec.id
                INNER JOIN empresas ep ON r.id_empresa_proveedor = ep.id
                WHERE r.id = :relacion_id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':relacion_id', $relacionId);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener detalle de relación: " . $e->getMessage());
        return false;
    }
}

// Función para obtener estadísticas de relaciones de una empresa
function obtenerEstadisticasRelaciones($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $sql = "SELECT 
                    COUNT(CASE WHEN id_empresa_cliente = :id_empresa THEN 1 END) as total_proveedores,
                    COUNT(CASE WHEN id_empresa_proveedor = :id_empresa THEN 1 END) as total_clientes,
                    COUNT(*) as total_relaciones
                FROM relaciones_empresa 
                WHERE (id_empresa_cliente = :id_empresa OR id_empresa_proveedor = :id_empresa)
                AND estado = 'activa'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_empresa', $idEmpresa);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado;
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de relaciones: " . $e->getMessage());
        return [
            'total_proveedores' => 0,
            'total_clientes' => 0,
            'total_relaciones' => 0
        ];
    }
}

?>