<?php
    require_once '../db/conexionDb.php';
    require_once '../model/Producto.php';
    function crearProducto($producto) {
        $db = new conexionDb();
        $conn = $db->getConnection();

        $sql = "INSERT INTO productos (codigo_seguimiento, nombre_producto, descripcion, id_categoria, ruta_imagen, precio, iva, id_empresa, activo, eliminado) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $producto->getCodigoSeguimiento(),
            $producto->getNombreProducto(),
            $producto->getDescripcion(),
            $producto->getIdCategoria(),
            $producto->getRutaImagen(),
            $producto->getPrecio(),
            $producto->getIva(),
            $producto->getIdEmpresa(),
            $producto->isActivo(),
            $producto->isEliminado()
        ]);

        $db->closeConnection();
    }

    function findByCodigoSeguimiento($codigoSeguimiento) {
        $db = new conexionDb();
        $conn = $db->getConnection();

        $idEmpresa = (string) $_SESSION['empresa']['id'];
        $sql = "SELECT * FROM productos WHERE codigo_seguimiento = ? AND id_empresa = ? AND eliminado = 'false'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$codigoSeguimiento, $idEmpresa[0]]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();

        if ($result) {
            return true;
        } else {
            return false;
        }
    }


    function findByEmpresaId() {
        $db = new conexionDb();
        $conn = $db->getConnection();

        $idEmpresa = (string) $_SESSION['empresa']['id'];
        $sql = "SELECT * FROM productos WHERE id_empresa = ? AND eliminado = 'false'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idEmpresa[0]]);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productos = array();

        foreach ($result as $producto) {
            $productos[] = new Producto(
                $producto['id'],
                $producto['codigo_seguimiento'],
                $producto['nombre_producto'],
                $producto['descripcion'],
                $producto['id_categoria'],
                $producto['ruta_imagen'],
                $producto['precio'],
                $producto['iva'],
                $producto['id_empresa'],
                $producto['fecha_creacion'],
                $producto['activo'],
                $producto['eliminado']
            );
        }

        $db->closeConnection();

        return $productos;
    }

    function eliminarProducto($idProducto) {
        $db = new conexionDb();
        $conn = $db->getConnection();
        $sql = "UPDATE productos SET eliminado = true WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idProducto]);

        $db->closeConnection();

        return $stmt->rowCount() > 0;
    }


    function findProductoEnEmpresa($idEmpresa, $idProducto){
        $db = new conexionDb();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM productos WHERE id = ? AND id_empresa = ? AND eliminado = 'false'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idProducto, $idEmpresa]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $db->closeConnection();

        if ($result) {
            return true;
        } else {
            return false;
        }

    }

    function findProductoById($idProducto){
        $db = new conexionDb();
        $conn = $db->getConnection();

        $sql = "SELECT * FROM productos WHERE id = ? AND eliminado = 'false'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$idProducto]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();

        if ($result) {
            $producto = new Producto(
                $result['id'],
                $result['codigo_seguimiento'],
                $result['nombre_producto'],
                $result['descripcion'],
                $result['id_categoria'],
                $result['ruta_imagen'],
                $result['precio'],
                $result['iva'],
                $result['id_empresa'],
                $result['fecha_creacion'],
                $result['activo'],
                $result['eliminado']
            );

            return $producto;
        } else {
            return null;
        }

    }


    function editarProducto($producto) {
        $db = new conexionDb();
        $conn = $db->getConnection();
        $sql = "UPDATE productos SET ";
        $params = [];
        if (!is_null($producto->getCodigoSeguimiento()) && $producto->getCodigoSeguimiento() !== '') {
            $sql .= "codigo_seguimiento = :codigo_seguimiento, ";
            $params['codigo_seguimiento'] = $producto->getCodigoSeguimiento();
        }
        if (!is_null($producto->getNombreProducto()) && $producto->getNombreProducto() !== '') {
            $sql .= "nombre_producto = :nombre_producto, ";
            $params['nombre_producto'] = $producto->getNombreProducto();
        }
        if (!is_null($producto->getDescripcion()) && $producto->getDescripcion() !== '') {
            $sql .= "descripcion = :descripcion, ";
            $params['descripcion'] = $producto->getDescripcion();
        }
        if (!is_null($producto->getIdCategoria()) && $producto->getIdCategoria() !== '') {
            $sql .= "id_categoria = :id_categoria, ";
            $params['id_categoria'] = $producto->getIdCategoria();
        }
        if (!is_null($producto->getRutaImagen()) && $producto->getRutaImagen() !== '') {
            $sql .= "ruta_imagen = :ruta_imagen, ";
            $params['ruta_imagen'] = $producto->getRutaImagen();
        }
        if (!is_null($producto->getPrecio()) && $producto->getPrecio() !== '') {
            $sql .= "precio = :precio, ";
            $params['precio'] = $producto->getPrecio();
        }
        if (!is_null($producto->getIva()) && $producto->getIva() !== '') {
            $sql .= "iva = :iva, ";
            $params['iva'] = $producto->getIva();
        }
        if (!is_null($producto->isActivo()) && $producto->isActivo() !== '') {
            $sql .= "activo = :activo, ";
            $params['activo'] = $producto->isActivo();
        }
        $sql = rtrim($sql, ", ");
        $sql .= " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $params['id'] = $producto->getId();
        $resultado = $stmt->execute($params);

        $db->closeConnection();

        return $resultado;
    }
?>