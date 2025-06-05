<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
    include_once '../constantes/constantesRutas.php';
    include_once RUTA_DB;
    include_once RUTA_EMPRESA_MODEL;
    include_once RUTA_EMPRESA_DAO;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selección de Empresas</title>
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/seleccionEmpresa.css">
</head>
<body>

    <div class="floating-shape shape1"></div>
    <div class="floating-shape shape2"></div>
    
    <div class="container">
        <h1>Selecciona una Empresa</h1>
        
        <div class="company-grid">
            <?php

                $empresasUsuario = findEmpresaByUserId($_SESSION['usuario']['id']);

                foreach ( $empresasUsuario as $empresa ) {
                    echo '<div class="company-card tarjetaEmpresaJs " data-empresa-id="' . $empresa->getId() . '">
                            <div class="logo-container">
                                <img src=" ../../' . $empresa->getRutaLogo() . '" alt="Logo Empresa ' . $empresa->getNombre() . '">
                            </div>
                            <h3 class="company-name">Empresa ' . $empresa->getNombre() . '</h3>
                            <p class="company-details">Sector: ' .  $empresa->getSector() . '</p>
                            <span class="company-status status-active">' . $empresa->getEstado() . '</span>
                            <input type="hidden" name="empresaId" value="' . $empresa->getId() . '">
                        </div>';
                }
            ?>  
            <!-- Añadir nueva empresa -->
            <div class="company-card add-company-card">
                <div class="add-icon">+</div>
                <p class="add-text">Añadir empresa</p>
            </div>

        </div>
    </div>
    <script src="../../public/js/seleccionEmpresa.js"></script>
        <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>