<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RemoteOrder - Explorador de Empresas</title>
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/menuLateral.css">
    <link rel="stylesheet" href="../../public/styles/clientes.css">
    <link rel="stylesheet" href="../../public/styles/solicitudes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <!-- Formas flotantes decorativas -->
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>

    <!-- Sidebar / Menú lateral -->
    <?php include 'elements/menuLateral.php'; ?>

    <!-- Contenido principal - Explorador de Empresas -->
    <div class="empresas-container">
        <div class="empresas-header">
            <h1 class="empresas-title">Mis Clientes</h1>
            <div class="empresas-actions">
                <button class="btn-view-favorites" id="btn-mis-solicitudes">
                    <i class="fas fa-envelope" style="color: #ffffff;"></i>
                    Mis Solicitudes
                </button>
            </div>
        </div>

        <div class="filters-container">
            <select class="filter-select" id="filter-sector">
                <option value="">Todos los sectores</option>
                <option value="tecnologia">Tecnología</option>
                <option value="servicios">Servicios Profesionales</option>
                <option value="comercio">Comercio</option>
                <option value="industria">Industria</option>
            </select>
        </div>

        <div class="empresas-table-container">
            <table class="empresas-table">
                <thead>
                    <tr>
                        <th>
                            <div class="th-content">
                                Empresa <i class="fas fa-sort"></i>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                Contacto <i class="fas fa-sort"></i>
                            </div>
                        </th>
                        <th>
                            <div class="th-content">
                                Sector <i class="fas fa-sort"></i>
                            </div>
                        </th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Cargar clientes relacionados con esta empresa (donde esta empresa es el proveedor)
                    require_once "../dao/RelacionesEmpresaDao.php";
                    
                    $idEmpresa = $_SESSION['empresa']['id'];
                    if (is_array($idEmpresa)) {
                        $idEmpresa = $idEmpresa[0];
                    }
                    
                    // Obtener clientes (empresas que nos han contratado como proveedores)
                    $clientes = obtenerClientesDeProveedor($idEmpresa);
                    
                    if (empty($clientes)): ?>
                    <tr>
                        <td colspan="4" class="text-center">No hay clientes vinculados. Las empresas que te contraten aparecerán aquí.</td>
                    </tr>
                    <?php else:
                        foreach ($clientes as $cliente): 
                            $iniciales = strtoupper(substr($cliente['nombre'], 0, 2));
                        ?>
                        <tr>
                            <td>
                                <div class="empresa-info">
                                    <?php if ($cliente['ruta_logo']): ?>
                                    <img src="../../<?php echo $cliente['ruta_logo']; ?>" alt="Logo <?php echo $cliente['nombre']; ?>" class="empresa-avatar" />
                                    <?php else: ?>
                                    <div class="empresa-avatar"><?php echo $iniciales; ?></div>
                                    <?php endif; ?>
                                    <div class="empresa-details">
                                        <div class="empresa-name"><?php echo $cliente['nombre']; ?></div>
                                        <div class="empresa-description"><?php echo substr($cliente['descripcion'], 0, 50) . (strlen($cliente['descripcion']) > 50 ? '...' : ''); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="empresa-contact">
                                    <div><?php echo $cliente['email']; ?></div>
                                    <div><?php echo $cliente['telefono']; ?></div>
                                </div>
                            </td>
                            <td><span class="tag tag-<?php echo strtolower($cliente['sector']); ?>"><?php echo $cliente['sector']; ?></span></td>
                            <td>
                                <div class="acciones-container">
                                    <button class="btn-action btn-view" title="Ver perfil" data-empresa-id="<?php echo $cliente['id']; ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-action btn-contact" title="Contactar" data-empresa-id="<?php echo $cliente['id']; ?>">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    <button class="btn-action btn-remove" title="Terminar relación" data-relacion-id="<?php echo $cliente['relacion_id']; ?>" data-empresa-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach;
                    endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Vista de tarjetas para móvil -->
        <div class="empresas-cards">
            <?php if (empty($clientes)): ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No hay clientes vinculados. Las empresas que te contraten aparecerán aquí.</p>
            </div>
            <?php else:
                foreach ($clientes as $cliente): 
                    $iniciales = strtoupper(substr($cliente['nombre'], 0, 2));
                ?>
                <div class="empresa-card" data-id="<?php echo $cliente['id']; ?>">
                    <div class="empresa-card-header">
                        <div class="empresa-info">
                            <?php if ($cliente['ruta_logo']): ?>
                            <img src="../../<?php echo $cliente['ruta_logo']; ?>" alt="Logo <?php echo $cliente['nombre']; ?>" class="empresa-avatar" />
                            <?php else: ?>
                            <div class="empresa-avatar"><?php echo $iniciales; ?></div>
                            <?php endif; ?>
                            <div class="empresa-details">
                                <div class="empresa-name"><?php echo $cliente['nombre']; ?></div>
                                <div class="empresa-description"><?php echo substr($cliente['descripcion'], 0, 50) . (strlen($cliente['descripcion']) > 50 ? '...' : ''); ?></div>
                            </div>
                        </div>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                            <span>4.5</span>
                        </div>
                    </div>
                    <div class="empresa-card-body">
                        <div class="info-row">
                            <div class="info-label">Email:</div>
                            <div class="info-value"><?php echo $cliente['email']; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Teléfono:</div>
                            <div class="info-value"><?php echo $cliente['telefono']; ?></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Sector:</div>
                            <div class="info-value"><span class="tag tag-<?php echo strtolower($cliente['sector']); ?>"><?php echo $cliente['sector']; ?></span></div>
                        </div>
                        <div class="info-row">
                            <div class="info-label">Ubicación:</div>
                            <div class="info-value"><?php echo $cliente['ciudad'] . ', ' . $cliente['pais']; ?></div>
                        </div>
                    </div>
                    <div class="empresa-card-footer">
                        <button class="btn-action btn-view" title="Ver perfil" data-empresa-id="<?php echo $cliente['id']; ?>">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action btn-contact" title="Contactar" data-empresa-id="<?php echo $cliente['id']; ?>">
                            <i class="fas fa-envelope"></i>
                        </button>
                        <button class="btn-action btn-remove" title="Terminar relación" data-relacion-id="<?php echo $cliente['relacion_id']; ?>" data-empresa-nombre="<?php echo htmlspecialchars($cliente['nombre']); ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>

    <!-- Modal for viewing solicitudes -->
    <div id="modal-solicitudes" class="modal">
        <div class="modal-content modal-solicitudes-content">
            <div class="modal-header">
                <h2>Mis Solicitudes</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <div class="solicitudes-tabs">
                    <button class="solicitud-tab-btn active" data-tab="enviadas">Enviadas</button>
                    <button class="solicitud-tab-btn" data-tab="recibidas">Recibidas</button>
                </div>
                
                <div class="solicitudes-content">
                    <div class="solicitud-tab-content active" id="tab-enviadas">
                        <div id="solicitudes-enviadas-container">
                            <div class="loading-spinner">Cargando solicitudes...</div>
                        </div>
                    </div>
                    
                    <div class="solicitud-tab-content" id="tab-recibidas">
                        <div id="solicitudes-recibidas-container">
                            <div class="loading-spinner">Cargando solicitudes...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../../public/js/solicitudes.js"></script>
    <script src="../../public/js/menuLateral.js"></script>
    <script src="../../public/js/clientes.js"></script>
    <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>