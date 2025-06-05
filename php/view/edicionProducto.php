<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once '../dao/categoriaDao.php';
    include_once '../model/categoria.php';
    include_once '../dao/productoDao.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RemoteOrder - Crear Producto</title>
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/menuLateral.css">
    <link rel="stylesheet" href="../../public/styles/crearProducto.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Formas flotantes decorativas -->
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>

    <!-- Sidebar / Menú lateral -->
    <?php include 'elements/menuLateral.php'; ?>

        <!-- Formulario de creación de producto -->
        <div class="form-container">
            <h1 class="form-title">Crear Nuevo Producto</h1>
            <?php 
                $idProducto = $_GET['id'];
                $producto = findProductoById($idProducto);
                $idEmpresa = (string) $_SESSION['empresa']['id'];
                $categoriaSeleccionada = findCategoriaById($producto->getIdCategoria());

                if (findProductoEnEmpresa($idEmpresa, $idProducto)) {
                    echo '
                    <form id="productoForm" action="../actions/editarProducto.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="' . $idProducto . '">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="codigo_seguimiento">Código de Seguimiento *</label>
                                <input type="number" id="codigo_seguimiento" name="codigo_seguimiento" class="form-control" placeholder="Ej: 1234567890" required value="' . $producto->getCodigoSeguimiento() . '">
                                <span class="form-hint">Código único para seguimiento del producto</span>
                            </div>
                            
                            <div class="form-group">
                                <label for="nombre_producto">Nombre del Producto</label>
                                <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" placeholder="Nombre del producto" required value="' . $producto->getNombreProducto() . '">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control textarea-control" placeholder="Descripción detallada del producto" value="' . $producto->getDescripcion() . '"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="categoria">Categoría</label>
                                <select id="categoria" name="id_categoria" class="form-control" required">
                                    <option style="color: ' . $categoriaSeleccionada->getColor() . '" value=" '.  $categoriaSeleccionada->getId() .' ">' .  $categoriaSeleccionada->getNombre() . '</option>                                    ';
                                    
                                    $categorias = findCategoriaByEmpresaId($idEmpresa[0]);

                                    foreach ($categorias as $categoria) {
                                        if ($categoria->getId() != $categoriaSeleccionada->getId()){
                                            echo '<option style="color: ' . $categoria->getColor() . '" value="' . $categoria->getId() . '">' . $categoria->getNombre() . '</option>';
                                        }
                                    }
                        echo '  </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="precio">Precio (€)</label>
                                <input type="number" id="precio" name="precio" class="form-control" placeholder="0.00" step="0.01" min="0" required value="' . $producto->getPrecio() . '">
                                <i class="fas fa-euro-sign input-icon"></i>
                            </div>
                            
                            <div class="form-group">
                                <label for="iva">IVA (%)</label>
                                <input type="number" id="iva" name="iva" class="form-control" placeholder="21" step="0.01" min="0" max="100" required value="' . $producto->getIva() . '">
                                <i class="fas fa-percent input-icon"></i>
                            </div>
                        </div>
                        
                        <div class="image-upload" id="imageUploadContainer">
                            <div class="image-upload-icon">
                                <i class="fas fa-image"></i>
                            </div>
                            <div class="image-upload-text">Subir imagen del producto</div>
                            <div class="image-upload-hint">Formatos aceptados: JPG, PNG. Tamaño máximo: 2MB</div>
                            <input type="file" id="imagen" name="imagen" accept="image/*" style="display: none;">
                            <img id="previewImage" class="preview-image" src="" alt="Vista previa de la imagen" value="../../' . $producto->getRutaImagen() . '">
                        </div>
                        
                        <div class="checkbox-container">
                            <input type="checkbox" id="activo" name="activo" checked>
                            <label for="activo">Producto activo (visible en catálogo)</label>
                        </div>
                        
                        <!-- Campo oculto para ID de empresa, normalmente se obtendría de la sesión -->
                        <input type="hidden" id="id_empresa" name="id_empresa" value="1">
                        
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" onclick="window.history.back()">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Producto</button>
                        </div>
                    </form>
                    ';
                }else{
                    header("Location: ../view/productos.php");
                    exit;
                }
            ?>
        </div>
    </div>

    <script src="../../public/js/creacionProducto.js"></script>
    <script src="../../public/js/menuLateral.js"></script>
        <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>

