<?php
    if (!defined('RUTA_DB')) {
        include_once '../constantes/constantesRutas.php';
    }
    include_once RUTA_DB;
    include_once RUTA_CATEGORIA_MODEL;

    function findCategoriaByEmpresaId($idEmpresa) {
        $categorias = [];
        try {
            $db = new conexionDb();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM categorias WHERE id_empresa = :id_empresa");
            $stmt->bindParam(':id_empresa', $idEmpresa);
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $categorias[] = new Categoria(
                    $row['id'],
                    $row['nombre_categoria'], 
                    $row['descripcion_categoria'], 
                    $row['color_categoria'], 
                    $row['id_empresa'], 
                    $row['fecha_creacion']
                );
            }
            $db->closeConnection();
        } catch (Exception $e) {
            echo "Error al buscar las categorias: " . $e->getMessage();
        }

        return $categorias;
    }

    function guardarCategoria($categoria) {
        try {
            $db = new conexionDb();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("INSERT INTO categorias (nombre_categoria, descripcion_categoria, color_categoria, id_empresa, fecha_creacion) VALUES (:nombre_categoria, :descripcion_categoria, :color_categoria, :id_empresa, :fecha_creacion)");
            $stmt->execute([
                ':nombre_categoria' => $categoria->getNombre(),
                ':descripcion_categoria' => $categoria->getDescripcion(),
                ':color_categoria' => $categoria->getColor(),
                ':id_empresa' => $categoria->getEmpresaId(),
                ':fecha_creacion' => $categoria->getFechaCreacion()
            ]);
        } catch (Exception $e) {
            echo "Error al guardar la categoria: " . $e->getMessage();
        }
    }

    function eliminarCategoria($idCategoria) {
        try {
            $db = new conexionDb();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("DELETE FROM categorias WHERE id = :id_categoria");
            $stmt->bindParam(':id_categoria', $idCategoria);
            $stmt->execute();
        } catch (Exception $e) {
            echo "Error al eliminar la categoria: " . $e->getMessage();
        }
    }

    function findCategoriaById($idCategoria) {
        try {
            $db = new conexionDb();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = :id_categoria");
            $stmt->bindParam(':id_categoria', $idCategoria);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $categoria = new Categoria(
                $row['id'],
                $row['nombre_categoria'], 
                $row['descripcion_categoria'], 
                $row['color_categoria'], 
                $row['id_empresa'], 
                $row['fecha_creacion']
            );
        } catch (Exception $e) {
            echo "Error al buscar la categoria: " . $e->getMessage();
        }
        return $categoria;
    }
    
    
?>