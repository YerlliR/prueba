<?php
// Determinar la página actual basada en el nombre del archivo
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Mapeo de páginas a nombres de etiquetas
$menu_items = [
    'panelPrincipal' => 'Dashboard',
    'pedidos-recibidos' => 'Pedidos Recibidos',
    'pedidos-enviados' => 'Pedidos Enviados',
    'productos' => 'Productos',
    'clientes' => 'Clientes',
    'proveedores' => 'Proveedores',
    'misProveedores' => 'Mis Proveedores',
    'explorarProveedores' => 'Explorar Proveedores',
    'facturacion' => 'Facturación'
];

// Determinar si estamos en la sección de pedidos
$in_pedidos_section = $current_page == 'pedidos-recibidos' || $current_page == 'pedidos-enviados';

// Determinar si estamos en la sección de proveedores2
$in_proveedores_section = $current_page == 'misProveedores' || $current_page == 'explorarProveedores';
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>RemoteOrder</h2>
        <small>Panel de Control</small>
    </div>
    <div class="menu-items">
        <!-- CORREGIDO: Añadido enlace directo para asegurar navegación -->
        <a href="../../php/view/panelPrincipal.php" class="menu-item <?php echo ($current_page == 'panelPrincipal') ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-item has-submenu <?php echo ($in_pedidos_section) ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Pedidos</span>
            <i class="fas fa-chevron-down" style="margin-left: auto; transform: <?php echo ($in_pedidos_section) ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
        </div>
        <div class="submenu <?php echo ($in_pedidos_section) ? 'submenu-active' : ''; ?>">
            <!-- CORREGIDO: Enlaces directos para submenú -->
            <a href="../../php/view/pedidos-recibidos.php" class="submenu-item <?php echo ($current_page == 'pedidos-recibidos') ? 'active' : ''; ?>">
                Recibidos
            </a>
            <a href="../../php/view/pedidos-enviados.php" class="submenu-item <?php echo ($current_page == 'pedidos-enviados') ? 'active' : ''; ?>">
                Enviados
            </a>
        </div>
        
        <!-- CORREGIDO: Enlaces directos para cada sección -->
        <a href="../../php/view/productos.php" class="menu-item <?php echo ($current_page == 'productos') ? 'active' : ''; ?>">
            <i class="fas fa-box"></i>
            <span>Productos</span>
        </a>
        
        <a href="../../php/view/clientes.php" class="menu-item <?php echo ($current_page == 'clientes') ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Clientes</span>
        </a>
        
        <div class="menu-item has-submenu <?php echo ($in_proveedores_section) ? 'active' : ''; ?>">
            <i class="fas fa-truck"></i>
            <span>Proveedores</span>
            <i class="fas fa-chevron-down" style="margin-left: auto; transform: <?php echo ($in_proveedores_section) ? 'rotate(180deg)' : 'rotate(0)'; ?>;"></i>
        </div>
        <div class="submenu <?php echo ($in_proveedores_section) ? 'submenu-active' : ''; ?>">
            <a href="../../php/view/misProveedores.php" class="submenu-item <?php echo ($current_page == 'misProveedores') ? 'active' : ''; ?>">
                Mis Proveedores
            </a>
            <a href="../../php/view/explorarProveedores.php" class="submenu-item <?php echo ($current_page == 'explorarProveedores') ? 'active' : ''; ?>">
                Explorar Proveedores
            </a>
        </div>
        
        <a href="../../php/view/facturacion.php" class="menu-item <?php echo ($current_page == 'facturacion') ? 'active' : ''; ?>">
            <i class="fas fa-file-invoice-dollar"></i>
            <span>Facturación</span>
        </a>
    </div>
</div>

<div class="main-content">
    <div class="header">
        <div class="header-left">
            <div class="toggle-sidebar" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </div>
            <div class="header-title">
                <?php
                // Mostrar el título correspondiente a la página actual
                if (isset($menu_items[$current_page])) {
                    echo $menu_items[$current_page];
                } elseif ($current_page == 'creacionProducto') {
                    echo 'Crear Producto';
                } elseif ($current_page == 'edicionProducto') {
                    echo 'Editar Producto';
                } else {
                    // Si estamos en una página que no está en el menú
                    echo ucfirst(str_replace('-', ' ', $current_page));
                }
                ?>
            </div>
        </div>
        <div class="user-menu">
            <div class="user-button" id="userButton">
                <div class="user-avatar">
                    <?php 
                    // Mostrar la inicial del nombre de usuario si está disponible
                    if (isset($_SESSION['usuario']['nombre'])) {
                        echo strtoupper(substr($_SESSION['usuario']['nombre'], 0, 1));
                    } else {
                        echo "U";
                    }
                    ?>
                </div>
                <div class="user-name">
                    <?php 
                    // Mostrar el nombre de usuario si está disponible
                    if (isset($_SESSION['usuario']['nombre'])) {
                        echo $_SESSION['usuario']['nombre'];
                    } else {
                        echo "Usuario";
                    }
                    ?>
                </div>
                <i class="fas fa-chevron-down"></i>
            </div>
            <div class="user-dropdown" id="userDropdown">
                <!-- CORREGIDO: Enlaces directos para opciones de usuario -->
                <a href="../../php/view/seleccionEmpresa.php" class="dropdown-item">
                    <i class="fas fa-building"></i>
                    <span>Cambiar Empresa</span>
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Ajustes</span>
                </a>
                <a href="../../php/actions/cerrarSesion.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </div>
        </div>
    </div>