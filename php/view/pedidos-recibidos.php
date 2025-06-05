<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit;
}

// Incluir archivos necesarios
require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once '../model/Pedido.php';
require_once '../dao/PedidoDao.php';

// Obtener pedidos recibidos
$idEmpresa = $_SESSION['empresa']['id'];
if (is_array($idEmpresa)) {
    $idEmpresa = $idEmpresa[0];
}
$pedidosRecibidos = findPedidosRecibidos($idEmpresa);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Recibidos - RemoteOrder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/menuLateral.css">
    <link rel="stylesheet" href="../../public/styles/pedidos-vista.css">
</head>
<body>
    <?php include_once 'elements/menuLateral.php'; ?>
    
    <div class="pedidos-container">
        <div class="pedidos-header">
            <h1 class="section-title">Pedidos Recibidos</h1>
            <div class="pedidos-stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-inbox"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Total Pedidos</span>
                        <span class="stat-value"><?php echo count($pedidosRecibidos); ?></span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Pendientes</span>
                        <span class="stat-value">
                            <?php echo count(array_filter($pedidosRecibidos, function($p) { return $p->getEstado() == 'pendiente'; })); ?>
                        </span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-content">
                        <span class="stat-label">Completados</span>
                        <span class="stat-value">
                            <?php echo count(array_filter($pedidosRecibidos, function($p) { return $p->getEstado() == 'completado'; })); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filtros-container">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-input" placeholder="Buscar por número de pedido o cliente...">
            </div>
            <select class="filter-select" id="filter-estado">
                <option value="">Todos los estados</option>
                <option value="pendiente">Pendiente</option>
                <option value="procesando">Procesando</option>
                <option value="completado">Completado</option>
                <option value="cancelado">Cancelado</option>
            </select>
            <select class="filter-select" id="filter-fecha">
                <option value="">Todas las fechas</option>
                <option value="hoy">Hoy</option>
                <option value="semana">Esta semana</option>
                <option value="mes">Este mes</option>
            </select>
        </div>

        <!-- Lista de pedidos -->
        <div class="pedidos-lista">
            <?php if (empty($pedidosRecibidos)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No tienes pedidos recibidos aún</p>
                    <p class="text-muted">Los pedidos de tus clientes aparecerán aquí</p>
                </div>
            <?php else: ?>
                <?php foreach ($pedidosRecibidos as $pedido): ?>
                    <div class="pedido-card" data-pedido-id="<?php echo $pedido->getId(); ?>">
                        <div class="pedido-header">
                            <div class="pedido-info">
                                <h3 class="pedido-numero">#<?php echo $pedido->getNumeroPedido(); ?></h3>
                                <span class="pedido-cliente">
                                    <i class="fas fa-building"></i> <?php echo $pedido->nombreCliente; ?>
                                </span>
                            </div>
                            <div class="pedido-estado estado-<?php echo $pedido->getEstado(); ?>">
                                <?php echo ucfirst($pedido->getEstado()); ?>
                            </div>
                        </div>
                        
                        <div class="pedido-body">
                            <div class="pedido-detalles">
                                <div class="detalle-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Fecha: <?php echo date('d/m/Y', strtotime($pedido->getFechaPedido())); ?></span>
                                </div>
                                <?php if ($pedido->getFechaEntregaEstimada()): ?>
                                <div class="detalle-item">
                                    <i class="fas fa-truck"></i>
                                    <span>Entrega: <?php echo date('d/m/Y', strtotime($pedido->getFechaEntregaEstimada())); ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="detalle-item">
                                    <i class="fas fa-euro-sign"></i>
                                    <span>Total: <?php echo number_format($pedido->getTotal(), 2); ?> €</span>
                                </div>
                            </div>
                            
                            <?php if ($pedido->getNotas()): ?>
                            <div class="pedido-notas">
                                <i class="fas fa-sticky-note"></i>
                                <span><?php echo htmlspecialchars($pedido->getNotas()); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="pedido-footer">
                            <button class="btn-pedido btn-ver-detalle" onclick="verDetallePedido(<?php echo $pedido->getId(); ?>)">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </button>
                            <?php if ($pedido->getEstado() == 'pendiente'): ?>
                            <button class="btn-pedido btn-procesar" onclick="cambiarEstadoPedido(<?php echo $pedido->getId(); ?>, 'procesando')">
                                <i class="fas fa-cog"></i> Procesar
                            </button>
                            <?php elseif ($pedido->getEstado() == 'procesando'): ?>
                            <button class="btn-pedido btn-completar" onclick="cambiarEstadoPedido(<?php echo $pedido->getId(); ?>, 'completado')">
                                <i class="fas fa-check"></i> Completar
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para ver detalle del pedido -->
    <div id="modal-detalle-pedido" class="modal">
        <div class="modal-content modal-detalle-content">
            <div class="modal-header">
                <h2>Detalle del Pedido</h2>
                <button class="modal-close"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="detalle-pedido-content">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>

    <script src="../../public/js/menuLateral.js"></script>
    <script src="../../public/js/pedidos-vista.js"></script>
        <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>