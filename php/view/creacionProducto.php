<?php 
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include_once '../dao/categoriaDao.php';
    include_once '../model/categoria.php';
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
            
            <form id="productoForm" action="../actions/crearProducto.php" method="post" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-group">
                        <label for="codigo_seguimiento">Código de Seguimiento *</label>
                        <input type="number" id="codigo_seguimiento" name="codigo_seguimiento" class="form-control" placeholder="Ej: 1234567890" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="nombre_producto">Nombre del Producto</label>
                        <input type="text" id="nombre_producto" name="nombre_producto" class="form-control" placeholder="Nombre del producto" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="form-control textarea-control" placeholder="Descripción detallada del producto" ></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="categoria">Categoría</label>
                        <select id="categoria" name="id_categoria" class="form-control" required>
                            <option value="">Seleccionar categoría</option>
                            <?php
                                $idEmpresa = (string) $_SESSION['empresa']['id'];
                                $categorias = findCategoriaByEmpresaId($idEmpresa[0]);

                                foreach ($categorias as $categoria) {
                                    echo '<option style="color: ' . $categoria->getColor() . '" value="' . $categoria->getId() . '">' . $categoria->getNombre() . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="precio">Precio (€)</label>
                        <input type="number" id="precio" name="precio" class="form-control" placeholder="0.00" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="iva">IVA (%)</label>
                        <input type="number" id="iva" name="iva" class="form-control" placeholder="21" step="0.01" min="0" max="100">
                    </div>
                </div>
                
                <div class="image-upload" id="imageUploadContainer">
                    <div class="image-upload-icon">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="image-upload-text">Subir imagen del producto</div>
                    <div class="image-upload-hint">Formatos aceptados: JPG, PNG. Tamaño máximo: 2MB</div>
                    <input type="file" id="imagen" name="imagen" accept="image/*" style="display: none;">
                    <img id="previewImage" class="preview-image" src="" alt="Vista previa de la imagen">
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
        </div>
    </div>


    <script src="../../public/js/creacionProducto.js"></script>
    <script src="../../public/js/menuLateral.js"></script>
        <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>
