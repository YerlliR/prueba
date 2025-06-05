<?php
// ===== ARCHIVO: php/dao/SolicitudDao.php (FUNCIÓN CORREGIDA) =====

if (!defined('RUTA_DB')) {
    include_once '../constantes/constantesRutas.php';
}
include_once RUTA_DB;
include_once '../model/Solicitud.php';

// Función corregida para guardar solicitud
function guardarSolicitud($solicitud) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        // Para depuración, registrar los valores que se intentan insertar
        error_log("Guardando solicitud - id_solicitante=" . $solicitud->getIdEmpresaSolicitante() . 
                ", id_proveedor=" . $solicitud->getIdEmpresaProveedor() . 
                ", asunto=" . $solicitud->getAsunto());
        
        // Verificar que la conexión existe
        if (!$conn) {
            throw new Exception("No se pudo establecer conexión con la base de datos");
        }
        
        // Preparar la consulta
        $sql = "INSERT INTO solicitudes (id_empresa_solicitante, id_empresa_proveedor, asunto, mensaje, estado, fecha_creacion) 
                VALUES (:id_empresa_solicitante, :id_empresa_proveedor, :asunto, :mensaje, :estado, :fecha_creacion)";
        
        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL");
        }
        
        $params = [
            ':id_empresa_solicitante' => $solicitud->getIdEmpresaSolicitante(),
            ':id_empresa_proveedor' => $solicitud->getIdEmpresaProveedor(),
            ':asunto' => $solicitud->getAsunto(),
            ':mensaje' => $solicitud->getMensaje(),
            ':estado' => $solicitud->getEstado(),
            ':fecha_creacion' => $solicitud->getFechaCreacion()
        ];
        
        // Para depuración, registrar los parámetros
        error_log("Parámetros SQL: " . print_r($params, true));
        
        // Ejecutar la consulta
        $resultado = $stmt->execute($params);
        
        // Verificar si hubo errores
        if (!$resultado) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error SQL: " . $errorInfo[2]);
        }
        
        // Obtener el ID de la solicitud insertada
        $solicitudId = $conn->lastInsertId();
        
        error_log("Solicitud guardada con ID: " . $solicitudId);
        
        $db->closeConnection();
        
        return $resultado && $solicitudId > 0;
        
    } catch (Exception $e) {
        error_log("Error en guardarSolicitud: " . $e->getMessage());
        if (isset($db)) {
            $db->closeConnection();
        }
        return false;
    }
}

// Función para encontrar solicitud por ID
function findSolicitudById($id) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM solicitudes WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        if ($row) {
            return new Solicitud(
                $row['id'],
                $row['id_empresa_solicitante'],
                $row['id_empresa_proveedor'],
                $row['asunto'],
                $row['mensaje'],
                $row['estado'],
                $row['fecha_creacion']
            );
        }
        
        return null;
        
    } catch (Exception $e) {
        error_log("Error al buscar solicitud por ID: " . $e->getMessage());
        return null;
    }
}

// Función para actualizar estado de solicitud
function actualizarEstadoSolicitud($id, $nuevoEstado) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("UPDATE solicitudes SET estado = :estado, fecha_respuesta = NOW() WHERE id = :id");
        $resultado = $stmt->execute([
            ':estado' => $nuevoEstado,
            ':id' => $id
        ]);
        
        $db->closeConnection();
        return $resultado;
        
    } catch (Exception $e) {
        error_log("Error al actualizar estado de solicitud: " . $e->getMessage());
        return false;
    }
}

// Función corregida para encontrar solicitudes pendientes enviadas
function findSolicitudesPendientesEnviadas($idEmpresa) {
    $solicitudes = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM solicitudes WHERE id_empresa_solicitante = :id_empresa AND estado = 'pendiente' ORDER BY fecha_creacion DESC");
        $stmt->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $solicitudes[] = new Solicitud(
                $row['id'],
                $row['id_empresa_solicitante'],
                $row['id_empresa_proveedor'],
                $row['asunto'],
                $row['mensaje'],
                $row['estado'],
                $row['fecha_creacion']
            );
        }
        
        $db->closeConnection();
        
        error_log("Encontradas " . count($solicitudes) . " solicitudes pendientes enviadas para empresa " . $idEmpresa);
        
    } catch (Exception $e) {
        error_log("Error al buscar solicitudes enviadas pendientes: " . $e->getMessage());
    }
    
    return $solicitudes;
}

// Función corregida para encontrar solicitudes pendientes recibidas
function findSolicitudesPendientesRecibidas($idEmpresa) {
    $solicitudes = [];
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT * FROM solicitudes WHERE id_empresa_proveedor = :id_empresa AND estado = 'pendiente' ORDER BY fecha_creacion DESC");
        $stmt->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $solicitudes[] = new Solicitud(
                $row['id'],
                $row['id_empresa_solicitante'],
                $row['id_empresa_proveedor'],
                $row['asunto'],
                $row['mensaje'],
                $row['estado'],
                $row['fecha_creacion']
            );
        }
        
        $db->closeConnection();
        
        error_log("Encontradas " . count($solicitudes) . " solicitudes pendientes recibidas para empresa " . $idEmpresa);
        
    } catch (Exception $e) {
        error_log("Error al buscar solicitudes recibidas pendientes: " . $e->getMessage());
    }
    
    return $solicitudes;
}

// Función adicional para verificar si existe una solicitud entre dos empresas
function existeSolicitudPendiente($idEmpresaSolicitante, $idEmpresaProveedor) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM solicitudes 
            WHERE id_empresa_solicitante = :id_solicitante 
            AND id_empresa_proveedor = :id_proveedor 
            AND estado = 'pendiente'
        ");
        
        $stmt->bindParam(':id_solicitante', $idEmpresaSolicitante, PDO::PARAM_INT);
        $stmt->bindParam(':id_proveedor', $idEmpresaProveedor, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $result['total'] > 0;
        
    } catch (Exception $e) {
        error_log("Error al verificar solicitud pendiente: " . $e->getMessage());
        return false;
    }
}

// Función para obtener estadísticas de solicitudes
function obtenerEstadisticasSolicitudes($idEmpresa) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("
            SELECT 
                SUM(CASE WHEN id_empresa_solicitante = :id_empresa THEN 1 ELSE 0 END) as enviadas,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa THEN 1 ELSE 0 END) as recibidas,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa AND estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes_respuesta,
                SUM(CASE WHEN id_empresa_solicitante = :id_empresa AND estado = 'aceptada' THEN 1 ELSE 0 END) as aceptadas_enviadas,
                SUM(CASE WHEN id_empresa_proveedor = :id_empresa AND estado = 'aceptada' THEN 1 ELSE 0 END) as aceptadas_recibidas
            FROM solicitudes 
            WHERE (id_empresa_solicitante = :id_empresa OR id_empresa_proveedor = :id_empresa)
        ");
        
        $stmt->bindParam(':id_empresa', $idEmpresa, PDO::PARAM_INT);
        $stmt->execute();
        
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $resultado ?: [
            'enviadas' => 0,
            'recibidas' => 0,
            'pendientes_respuesta' => 0,
            'aceptadas_enviadas' => 0,
            'aceptadas_recibidas' => 0
        ];
        
    } catch (Exception $e) {
        error_log("Error al obtener estadísticas de solicitudes: " . $e->getMessage());
        return [
            'enviadas' => 0,
            'recibidas' => 0,
            'pendientes_respuesta' => 0,
            'aceptadas_enviadas' => 0,
            'aceptadas_recibidas' => 0
        ];
    }
}
?>