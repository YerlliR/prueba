<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar autenticación
if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
    header('Location: login.php');
    exit;
}

// Incluir dependencias
require_once '../dao/DashbordDao.php';

// Obtener ID de la empresa
$idEmpresa = $_SESSION['empresa']['id'];
if (is_array($idEmpresa)) {
    $idEmpresa = $idEmpresa[0];
}

// Obtener datos del dashboard
$datosPanel = obtenerDatosDashboard($idEmpresa);
$pedidosRecibidos = $datosPanel['pedidos_recibidos'];
$pedidosEnviados = $datosPanel['pedidos_enviados'];
$productos = $datosPanel['productos'];
$solicitudes = $datosPanel['solicitudes'];
$relaciones = $datosPanel['relaciones'];
$pedidosRecibidosRecientes = $datosPanel['pedidos_recibidos_recientes'];
$pedidosEnviadosRecientes = $datosPanel['pedidos_enviados_recientes'];

// Función helper para formatear estado
function formatearEstado($estado) {
    $estados = [
        'pendiente' => 'Pendiente',
        'procesando' => 'En Proceso',
        'completado' => 'Completado',
        'cancelado' => 'Cancelado'
    ];
    return $estados[$estado] ?? ucfirst($estado);
}

// Función helper para formatear fecha
function formatearFecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}

// Función helper para formatear precio
function formatearPrecio($precio) {
    return number_format($precio, 2, ',', '.') . ' €';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/menuLateral.css">
    <link rel="stylesheet" href="../../public/styles/panelPrincipal.css">
</head>
<body>
    <!-- Elementos decorativos -->
    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>

    <!-- Sidebar / Menú lateral -->
    <?php include 'elements/menuLateral.php'; ?>

    <!-- Contenido principal -->
    <div class="dashboard">
        <h2 class="dashboard-title">Resumen de Actividad</h2>
        
        <!-- Estadísticas principales -->
        <div class="dashboard-summary">
            <!-- Tarjeta de Pedidos Recibidos -->
            <div class="summary-card">
                <h3><i class="fas fa-inbox"></i> Pedidos Recibidos</h3>
                <div class="stats">
                    <div class="stat-item">Total: <strong><?php echo $pedidosRecibidos['total']; ?></strong></div>
                    <div class="stat-item">Pendientes: <strong><?php echo $pedidosRecibidos['pendientes']; ?></strong></div>
                    <div class="stat-item">En Proceso: <strong><?php echo $pedidosRecibidos['procesando']; ?></strong></div>
                    <div class="stat-item">Completados: <strong><?php echo $pedidosRecibidos['completados']; ?></strong></div>
                </div>
                <?php if ($pedidosRecibidos['ingresos_totales'] > 0): ?>
                <div class="ingresos-info">
                    <small>Ingresos: <strong><?php echo formatearPrecio($pedidosRecibidos['ingresos_totales']); ?></strong></small>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tarjeta de Pedidos Enviados -->
            <div class="summary-card">
                <h3><i class="fas fa-paper-plane"></i> Pedidos Enviados</h3>
                <div class="stats">
                    <div class="stat-item">Total: <strong><?php echo $pedidosEnviados['total']; ?></strong></div>
                    <div class="stat-item">Pendientes: <strong><?php echo $pedidosEnviados['pendientes']; ?></strong></div>
                    <div class="stat-item">En Proceso: <strong><?php echo $pedidosEnviados['en_proceso']; ?></strong></div>
                    <div class="stat-item">Completados: <strong><?php echo $pedidosEnviados['completados']; ?></strong></div>
                </div>
                <?php if ($pedidosEnviados['gastos_totales'] > 0): ?>
                <div class="gastos-info">
                    <small>Gastos: <strong><?php echo formatearPrecio($pedidosEnviados['gastos_totales']); ?></strong></small>
                </div>
                <?php endif; ?>
            </div>

            <!-- Tarjeta de Productos -->
            <div class="summary-card">
                <h3><i class="fas fa-box"></i> Productos</h3>
                <div class="stats">
                    <div class="stat-item">Total: <strong><?php echo $productos['total_productos']; ?></strong></div>
                    <div class="stat-item">Activos: <strong><?php echo $productos['productos_activos']; ?></strong></div>
                    <div class="stat-item">Inactivos: <strong><?php echo $productos['productos_inactivos']; ?></strong></div>
                    <div class="stat-item">Categorías: <strong><?php echo $productos['total_categorias']; ?></strong></div>
                </div>
            </div>

            <!-- Tarjeta de Relaciones Comerciales -->
            <div class="summary-card">
                <h3><i class="fas fa-handshake"></i> Relaciones Comerciales</h3>
                <div class="stats">
                    <div class="stat-item">Proveedores: <strong><?php echo $relaciones['proveedores']; ?></strong></div>
                    <div class="stat-item">Clientes: <strong><?php echo $relaciones['clientes']; ?></strong></div>
                    <div class="stat-item">Solicitudes Pendientes: <strong><?php echo $solicitudes['pendientes_respuesta']; ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Tabla de Pedidos Recibidos Recientes -->
        <?php if (!empty($pedidosRecibidosRecientes)): ?>
        <div class="order-table">
            <div class="table-header">
                <h3>Pedidos Recibidos Recientes</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidosRecibidosRecientes as $pedido): ?>
                    <tr>
                        <td>#<?php echo $pedido['numero_pedido']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['nombre_cliente']); ?></td>
                        <td><?php echo formatearFecha($pedido['fecha_pedido']); ?></td>
                        <td><?php echo formatearPrecio($pedido['total']); ?></td>
                        <td>
                            <span class="status status-<?php echo $pedido['estado']; ?>">
                                <?php echo formatearEstado($pedido['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="pedidos-recibidos.php" class="btn btn-sm btn-primary">Ver Detalle</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="table-footer">
                <a href="pedidos-recibidos.php" class="btn btn-primary">Ver Todos los Pedidos Recibidos</a>
            </div>
        </div>
        <?php else: ?>
        <div class="order-table">
            <div class="table-header">
                <h3>Pedidos Recibidos Recientes</h3>
            </div>
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No has recibido pedidos aún</p>
                <p class="text-muted">Los pedidos de tus clientes aparecerán aquí</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabla de Pedidos Enviados Recientes -->
        <?php if (!empty($pedidosEnviadosRecientes)): ?>
        <div class="order-table" style="margin-top: 30px;">
            <div class="table-header">
                <h3>Pedidos Enviados Recientes</h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pedidosEnviadosRecientes as $pedido): ?>
                    <tr>
                        <td>#<?php echo $pedido['numero_pedido']; ?></td>
                        <td><?php echo htmlspecialchars($pedido['nombre_proveedor']); ?></td>
                        <td><?php echo formatearFecha($pedido['fecha_pedido']); ?></td>
                        <td><?php echo formatearPrecio($pedido['total']); ?></td>
                        <td>
                            <span class="status status-<?php echo $pedido['estado']; ?>">
                                <?php echo formatearEstado($pedido['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <a href="pedidos-enviados.php" class="btn btn-sm btn-primary">Ver Detalle</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="table-footer">
                <a href="pedidos-enviados.php" class="btn btn-primary">Ver Todos los Pedidos Enviados</a>
            </div>
        </div>
        <?php else: ?>
        <div class="order-table" style="margin-top: 30px;">
            <div class="table-header">
                <h3>Pedidos Enviados Recientes</h3>
            </div>
            <div class="empty-state">
                <i class="fas fa-paper-plane"></i>
                <p>No has realizado pedidos aún</p>
                <p class="text-muted">Explora proveedores para realizar tu primer pedido</p>
                <a href="explorarProveedores.php" class="btn btn-primary" style="margin-top: 15px;">
                    <i class="fas fa-search"></i> Explorar Proveedores
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Sección de acciones rápidas -->
        <div class="quick-actions" style="margin-top: 40px;">
            <h3 style="margin-bottom: 20px; color: var(--primary-color);">Acciones Rápidas</h3>
            <div class="actions-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <a href="productos.php" class="action-card" style="background: white; padding: 20px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); text-decoration: none; text-align: center; transition: var(--transition);">
                    <i class="fas fa-plus-circle" style="font-size: 24px; color: var(--primary-color); margin-bottom: 10px;"></i>
                    <div style="color: var(--text-color); font-weight: 500;">Gestionar Productos</div>
                </a>
                <a href="explorarProveedores.php" class="action-card" style="background: white; padding: 20px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); text-decoration: none; text-align: center; transition: var(--transition);">
                    <i class="fas fa-search" style="font-size: 24px; color: var(--primary-color); margin-bottom: 10px;"></i>
                    <div style="color: var(--text-color); font-weight: 500;">Buscar Proveedores</div>
                </a>
                <a href="misProveedores.php" class="action-card" style="background: white; padding: 20px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); text-decoration: none; text-align: center; transition: var(--transition);">
                    <i class="fas fa-truck" style="font-size: 24px; color: var(--primary-color); margin-bottom: 10px;"></i>
                    <div style="color: var(--text-color); font-weight: 500;">Mis Proveedores</div>
                </a>
                <a href="clientes.php" class="action-card" style="background: white; padding: 20px; border-radius: var(--border-radius-md); box-shadow: var(--shadow-sm); text-decoration: none; text-align: center; transition: var(--transition);">
                    <i class="fas fa-users" style="font-size: 24px; color: var(--primary-color); margin-bottom: 10px;"></i>
                    <div style="color: var(--text-color); font-weight: 500;">Gestionar Clientes</div>
                </a>
            </div>
        </div>
    </div>

    <!-- CSS adicional para mejorar la presentación -->
    <style>
        .table-footer {
            padding: 15px 20px;
            text-align: center;
            background-color: var(--bg-color);
            border-top: 1px solid var(--border-color);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }
        
        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-light);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        .empty-state p {
            margin: 5px 0;
        }
        
        .text-muted {
            color: var(--text-light);
            font-size: 14px;
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md) !important;
        }
        
        .ingresos-info, .gastos-info {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-color);
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .actions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .action-card {
                padding: 15px !important;
            }
            
            .stats {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }
    </style>

    <!-- Incluir el archivo JavaScript del menú lateral -->
    <script src="../../public/js/menuLateral.js"></script>
    <?php include_once '../includes/footer_alerts.php'; ?>
</body>
</html>