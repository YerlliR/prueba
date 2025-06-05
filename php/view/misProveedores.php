<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit;
}

// Incluir los archivos necesarios
require_once "../dao/RelacionesEmpresaDao.php";
require_once "../model/Empresa.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Proveedores - RemoteOrder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/menuLateral.css">
    <link rel="stylesheet" href="../../public/styles/misProveedores.css">
    <link rel="stylesheet" href="../../public/styles/pedidos.css">
</head>
<body>
    <?php include_once 'elements/menuLateral.php'; ?>
    
    <div class="proveedores-container">
        <div class="proveedores-header">
            <h1 class="section-title">Mis Proveedores</h1>
            <div class="proveedores-actions">
                <div class="search-container">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" class="search-input" placeholder="Buscar proveedor...">
                </div>
                <a href="explorarProveedores.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Añadir Proveedor
                </a>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-container">
            <select class="filter-select" id="filter-sector">
                <option value="">Todos los sectores</option>
                <option value="tecnologia">Tecnología</option>
                <option value="servicios">Servicios Profesionales</option>
                <option value="comercio">Comercio</option>
                <option value="industria">Industria</option>
                <option value="agricola">Agrícola</option>
                <option value="alimenticio">Alimenticio</option>
            </select>
        </div>

        <!-- Vista de lista de proveedores -->
        <div class="proveedores-lista">
            <div class="header-row">
                <div class="header-cell">Proveedor</div>
                <div class="header-cell">Sector</div>
                <div class="header-cell">Contacto</div>
                <div class="header-cell">Ubicación</div>
                <div class="header-cell">Acciones</div>
            </div>

            <?php
            // Obtener el ID de la empresa actual
            $idEmpresa = $_SESSION['empresa']['id'];
            if (is_array($idEmpresa)) {
                $idEmpresa = $idEmpresa[0]; // Si es un array, tomar el primer elemento
            }
            
            // Obtener los proveedores de la empresa actual
            $proveedores = obtenerProveedoresDeProveedor($idEmpresa);
            
            if (empty($proveedores)) {
                echo '<div class="empty-state" style="padding: 40px; text-align: center; width: 100%;">
                    <i class="fas fa-store" style="font-size: 48px; margin-bottom: 20px; color: #cbd5e1;"></i>
                    <p>No tienes proveedores vinculados actualmente.</p>
                    <p>Explora nuevos proveedores para establecer relaciones comerciales.</p>
                    <a href="explorarProveedores.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">
                        Explorar Proveedores
                    </a>
                </div>';
            } else {
                foreach ($proveedores as $proveedor) {
                    // Generar iniciales del nombre de la empresa para el avatar
                    $iniciales = strtoupper(substr($proveedor['nombre'], 0, 2));
                    
                    echo '<div class="proveedor-row">
                        <div class="proveedor-cell proveedor-info">
                            <div class="proveedor-avatar">';
                    if (!empty($proveedor['ruta_logo'])) {
                        echo '<img src="../../' . $proveedor['ruta_logo'] . '" alt="Logo ' . $proveedor['nombre'] . '">';
                    } else {
                        echo $iniciales;
                    }
                    echo '</div>
                            <div class="proveedor-details">
                                <h3 class="proveedor-name">' . $proveedor['nombre'] . '</h3>
                                <p class="proveedor-location"><i class="fas fa-map-marker-alt"></i> ' . $proveedor['ciudad'] . ', ' . $proveedor['pais'] . '</p>
                            </div>
                        </div>
                        <div class="proveedor-cell proveedor-sector">
                            <span class="tag tag-' . strtolower($proveedor['sector']) . '">' . $proveedor['sector'] . '</span>
                        </div>
                        <div class="proveedor-cell proveedor-contacto">
                            <p><i class="fas fa-envelope"></i> ' . $proveedor['email'] . '</p>
                            <p><i class="fas fa-phone"></i> ' . $proveedor['telefono'] . '</p>
                        </div>
                        <div class="proveedor-cell proveedor-ubicacion">
                            <p>' . $proveedor['ciudad'] . ', ' . $proveedor['pais'] . '</p>
                        </div>
                        <div class="proveedor-cell proveedor-acciones">
                            <button class="btn-action btn-order" title="Realizar pedido" data-id="' . $proveedor['id'] . '">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            <button class="btn-action btn-remove" title="Eliminar proveedor" data-id="' . $proveedor['relacion_id'] . '">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>

        <!-- Vista móvil - Tarjetas de proveedores -->
        <div class="proveedores-cards">
            <?php
            if (empty($proveedores)) {
                echo '<div class="empty-state" style="padding: 40px; text-align: center; width: 100%;">
                    <i class="fas fa-store" style="font-size: 48px; margin-bottom: 20px; color: #cbd5e1;"></i>
                    <p>No tienes proveedores vinculados actualmente.</p>
                    <p>Explora nuevos proveedores para establecer relaciones comerciales.</p>
                    <a href="explorarProveedores.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">
                        Explorar Proveedores
                    </a>
                </div>';
            } else {
                foreach ($proveedores as $proveedor) {
                    $iniciales = strtoupper(substr($proveedor['nombre'], 0, 2));
                    
                    echo '<div class="proveedor-card">
                        <div class="proveedor-card-header">
                            <div class="proveedor-avatar">';
                    if (!empty($proveedor['ruta_logo'])) {
                        echo '<img src="../../' . $proveedor['ruta_logo'] . '" alt="Logo ' . $proveedor['nombre'] . '">';
                    } else {
                        echo $iniciales;
                    }
                    echo '</div>
                            <div class="proveedor-details">
                                <h3 class="proveedor-name">' . $proveedor['nombre'] . '</h3>
                                <span class="tag tag-' . strtolower($proveedor['sector']) . '">' . $proveedor['sector'] . '</span>
                                <p class="proveedor-location"><i class="fas fa-map-marker-alt"></i> ' . $proveedor['ciudad'] . ', ' . $proveedor['pais'] . '</p>
                            </div>
                        </div>
                        <div class="proveedor-card-body">
                            <div class="card-row">
                                <span class="card-label">Contacto:</span>
                                <div class="card-value">
                                    <p><i class="fas fa-envelope"></i> ' . $proveedor['email'] . '</p>
                                    <p><i class="fas fa-phone"></i> ' . $proveedor['telefono'] . '</p>
                                </div>
                            </div>
                            <div class="card-row">
                                <span class="card-label">Sitio web:</span>
                                <div class="card-value">
                                    <p><i class="fas fa-globe"></i> ' . ($proveedor['sitio_web'] ?: 'No disponible') . '</p>
                                </div>
                            </div>
                            <div class="card-row">
                                <span class="card-label">Valoración:</span>
                                <div class="card-value rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                    <span>4.5</span>
                                </div>
                            </div>
                        </div>
                        <div class="proveedor-card-footer">
                            <button class="btn-action btn-view" title="Ver perfil" data-id="' . $proveedor['id'] . '">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-action btn-order" title="Realizar pedido" data-id="' . $proveedor['id'] . '">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                            <button class="btn-action btn-chat" title="Enviar mensaje" data-id="' . $proveedor['id'] . '">
                                <i class="fas fa-comment-alt"></i>
                            </button>
                            <button class="btn-action btn-remove" title="Eliminar proveedor" data-id="' . $proveedor['relacion_id'] . '">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>

    </div>


    <!-- Scripts -->
    <script src="../../public/js/menuLateral.js"></script>
    <script src="../../public/js/pedidos.js"></script>
    <script src="../../public/js/misProveedores.js"></script>
    
    <?php include_once '../includes/footer_alerts.php'; ?>
</body>
</html>