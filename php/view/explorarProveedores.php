<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../index.php');
    exit;
}


include_once "../dao/EmpresaDao.php"; 
include_once "../model/Empresa.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Explorar Proveedores - RemoteOrder</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../public/styles/base.css">
    <link rel="stylesheet" href="../../public/styles/menuLateral.css">
    <link rel="stylesheet" href="../../public/styles/exploradorProveedores.css">
</head>
<body>
    <?php include_once 'elements/menuLateral.php'; ?>
    
    <div class="marketplace-container">

        <!-- Categorías principales -->
        <div class="categories-section">
            <h2 style="margin-top: 5rem;">Explora por categoría</h2>
            <div class="categories-grid">
                <div class="category-card" data-category="tecnologia">
                    <div class="category-icon">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3>Tecnología</h3>
                    <span class="provider-count"><?php echo contBySector("tecnologia");?> proveedores</span>
                </div>
                <div class="category-card" data-category="finanzas">
                    <div class="category-icon">
                        <i class="fas fa-piggy-bank"></i>
                    </div>
                    <h3>Finanzas</h3>
                    <span class="provider-count"><?php echo contBySector("finanzas");?> proveedores</span>
                </div>
                <div class="category-card" data-category="marketing">
                    <div class="category-icon">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <h3>Marketing</h3>
                    <span class="provider-count"><?php echo contBySector("marketing");?> proveedores</span>
                </div>
                <div class="category-card" data-category="salud">
                    <div class="category-icon">
                        <i class="fas fa-medkit"></i>
                    </div>
                    <h3>Salud</h3>
                    <span class="provider-count"><?php echo contBySector("salud");?> proveedores</span>
                </div>
                <div class="category-card" data-category="educacion">
                    <div class="category-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <h3>Educación</h3>
                    <span class="provider-count"><?php echo contBySector("educacion");?> proveedores</span>
                </div>
                <div class="category-card" data-category="turismo">
                    <div class="category-icon">
                        <i class="fas fa-passport"></i>
                    </div>
                    <h3>Turismo</h3>
                    <span class="provider-count"><?php echo contBySector("turismo");?> proveedores</span>
                </div>
                <div class="category-card" data-category="inmobiliario">
                    <div class="category-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h3>Inmobiliario</h3>
                    <span class="provider-count"><?php echo contBySector("inmobiliario");?> proveedores</span>
                </div>
                <div class="category-card" data-category="industrial">
                    <div class="category-icon">
                        <i class="fas fa-industry"></i>
                    </div>
                    <h3>Industrial</h3>
                    <span class="provider-count"><?php echo contBySector("industrial");?> proveedores</span>
                </div>
                <div class="category-card" data-category="alimenticio">
                    <div class="category-icon">
                        <i class="fas fa-concierge-bell"></i>
                    </div>
                    <h3>Alimenticio</h3>
                    <span class="provider-count"><?php echo contBySector("alimenticio");?> proveedores</span>
                </div>
                <div class="category-card" data-category="comercio">
                    <div class="category-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3>Comercio</h3>
                    <span class="provider-count"><?php echo contBySector("comercio");?> proveedores</span>
                </div>
                <div class="category-card" data-category="agricola">
                    <div class="category-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3>Agrícola</h3>
                    <span class="provider-count"><?php echo contBySector("agricola");?> proveedores</span>
                </div>
                <div class="category-card" data-category="energia">
                    <div class="category-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h3>Energía</h3>
                    <span class="provider-count"><?php echo contBySector("energia");?> proveedores</span>
                </div>
                <div class="category-card" data-category="transporte">
                    <div class="category-icon">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h3>Transporte</h3>
                    <span class="provider-count"><?php echo contBySector("transporte");?> proveedores</span>
                </div>
                <div class="category-card" data-category="otros">
                    <div class="category-icon">
                        <i class="fas fa-question-circle"></i>
                    </div>
                    <h3>Otros</h3>
                    <span class="provider-count"><?php echo contBySector("otros");?> proveedores</span>
                </div>
            </div>
        </div>
        

        <!-- Sección de filtros y proveedores -->
        <div class="marketplace-content">
            <!-- Panel lateral de filtros -->
            <div class="filters-sidebar">
                <div class="filters-header">
                    <h3>Filtros</h3>
                    <button id="reset-filters" class="btn-reset">Restablecer</button>
                </div>
                
                <div class="filter-group">
                    <h4>Ubicación</h4>
                    <div class="filter-options">
                        <label class="filter-option">
                            <input type="checkbox" name="location" value="madrid"> Madrid
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="location" value="barcelona"> Barcelona
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="location" value="valencia"> Valencia
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="location" value="sevilla"> Sevilla
                        </label>
                        <label class="filter-option">
                            <input type="checkbox" name="location" value="bilbao"> Bilbao
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Proveedores -->
            <div class="providers-section">
                <div class="providers-header">
                    <div class="results-info">
                        <h2>Proveedores <span id="category-title"></span></h2>
                        <p>Mostrando todos los resultados</p>
                    </div>
                </div>
                
                <!-- Vista de cuadrícula (por defecto) -->
                <div class="providers-grid active-view" id="grid-view">
                    <?php

                        $empresas = findAllEmpresas();

                        foreach($empresas as $empresa){
                            if($empresa->getId() != $_SESSION['empresa']['id']){
                                echo '
                                    <div class="provider-card" data-id="'.$empresa->getId().'">
                                        <div class="provider-header">
                                            <div class="provider-logo"><img src="../../'.$empresa->getRutaLogo().'" alt="Logo de la empresa '.$empresa->getNombre().'" class="provider-logo"></div>
                                        </div>
                                        <div class="provider-body">
                                            <h3>'.$empresa->getNombre().'</h3>
                                            <div class="provider-location">
                                                <i class="fas fa-map-marker-alt"> </i> '.$empresa->getCiudad().', '.$empresa->getPais().'
                                            </div>
                                            <div class="provider-tags">
                                                <span class="tag tag-'.strtolower($empresa->getSector()).'">' . $empresa->getSector() . '</span>
                                            </div>
                                            <p class="provider-description">'. (strlen($empresa->getDescripcion()) > 200 ? substr($empresa->getDescripcion(), 0, 200) . '...' : $empresa->getDescripcion()) . '</p>
                                        </div>
                                        <div class="provider-footer">
                                            <button class="btn-view-profile" data-empresa-id="'.$empresa->getId().'">Ver perfil</button>
                                            <button class="btn-contact" data-empresa-id="'.$empresa->getId().'">Contactar</button>
                                        </div>
                                    </div>
                                
                                ';
                            }
                        }
                    
                    ?>
                    
                </div>
                
                <!-- Paginación -->

            </div>
        </div>
    </div>
    
    <!-- Modal de Contacto -->
    <!-- ===== MODAL DE CONTACTO CORREGIDO ===== -->
<!-- Agregar este HTML al final de explorarProveedores.php, antes del cierre de </body> -->

<div class="modal" id="modal-contact">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Contactar con <span id="contact-provider-name">Proveedor</span></h2>
            <button class="modal-close" type="button" aria-label="Cerrar modal">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <div class="contact-form">
                <div class="form-group">
                    <label for="modal-contact-subject">
                        Asunto <span style="color: #dc2626;">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="modal-contact-subject" 
                        class="form-control"
                        placeholder="Ej: Solicitud de presupuesto, Consulta sobre servicios..."
                        maxlength="100"
                        required
                    >
                    <small class="form-hint">Mínimo 5 caracteres, máximo 100</small>
                </div>
                
                <div class="form-group">
                    <label for="modal-contact-message">
                        Mensaje <span style="color: #dc2626;">*</span>
                    </label>
                    <textarea 
                        id="modal-contact-message" 
                        class="form-control textarea-control"
                        rows="6" 
                        placeholder="Describe tu consulta o solicitud de forma detallada...&#10;&#10;Por favor, incluye:&#10;- Qué productos o servicios te interesan&#10;- Plazos estimados&#10;- Cualquier información relevante"
                        maxlength="1000"
                        required
                    ></textarea>
                    <small class="form-hint">
                        Mínimo 20 caracteres, máximo 1000 
                        (<span id="message-counter">0</span>/1000)
                    </small>
                </div>
                
                <div class="form-group">
                    <div class="contact-info-card">
                        <div class="info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="info-content">
                            <h4>¿Qué sucede después?</h4>
                            <ul>
                                <li>Tu solicitud se enviará directamente al proveedor</li>
                                <li>Recibirás una notificación cuando respondan</li>
                                <li>Si aceptan, se establecerá una relación comercial</li>
                                <li>Podrás realizar pedidos directamente</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal-footer">
            <button 
                type="button" 
                class="btn btn-secondary" 
                id="btn-cancel-contact"
            >
                <i class="fas fa-times"></i>
                Cancelar
            </button>
            <button 
                type="button" 
                class="btn btn-primary" 
                id="btn-send-modal-message"
            >
                <i class="fas fa-paper-plane"></i>
                Enviar Solicitud
            </button>
        </div>
    </div>
</div>

<!-- Estilos adicionales para el modal mejorado -->
<style>
.contact-form .form-group {
    margin-bottom: 20px;
}

.contact-form label {
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
    color: var(--text-color);
}

.contact-form .form-control {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    font-family: inherit;
}

.contact-form .form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.contact-form .form-control.error {
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.contact-form .textarea-control {
    resize: vertical;
    min-height: 120px;
    line-height: 1.5;
}

.form-hint {
    display: block;
    font-size: 12px;
    color: var(--text-light);
    margin-top: 4px;
}

.error-message {
    color: #dc2626;
    font-size: 12px;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 4px;
}

.error-message::before {
    content: "⚠";
}

.contact-info-card {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 10px;
    padding: 16px;
    margin-top: 10px;
    display: flex;
    gap: 12px;
}

.info-icon {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
    margin-top: 2px;
}

.info-content h4 {
    margin: 0 0 8px 0;
    color: var(--primary-color);
    font-size: 14px;
    font-weight: 600;
}

.info-content ul {
    margin: 0;
    padding-left: 16px;
    font-size: 13px;
    color: var(--text-color);
    line-height: 1.4;
}

.info-content li {
    margin-bottom: 4px;
}

#message-counter {
    font-weight: 600;
    color: var(--primary-color);
}

/* Estados del botón */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

.btn:disabled:hover {
    transform: none !important;
}

/* Responsive */
@media (max-width: 576px) {
    .contact-info-card {
        flex-direction: column;
        text-align: center;
    }
    
    .modal-footer {
        flex-direction: column;
        gap: 10px;
    }
    
    .modal-footer .btn {
        width: 100%;
    }
}
</style>

<!-- Script adicional para funcionalidades del modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contador de caracteres para el mensaje
    const messageTextarea = document.getElementById('modal-contact-message');
    const messageCounter = document.getElementById('message-counter');
    
    if (messageTextarea && messageCounter) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            messageCounter.textContent = length;
            
            // Cambiar color según la proximidad al límite
            if (length > 900) {
                messageCounter.style.color = '#dc2626';
            } else if (length > 800) {
                messageCounter.style.color = '#d97706';
            } else {
                messageCounter.style.color = 'var(--primary-color)';
            }
        });
    }
    
    // Limpiar estilos de error al escribir
    const formInputs = document.querySelectorAll('#modal-contact input, #modal-contact textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.style.borderColor = '';
            
            // Remover mensajes de error
            const errorMsg = this.parentNode.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.remove();
            }
        });
    });
});
</script>

    <script src="../../public/js/menuLateral.js"></script>
    <script src="../../public/js/exploradorProveedores.js"></script>


    <!-- Después de los scripts existentes, al final del archivo -->
    <script>
        // Almacenar datos de empresas para uso en JavaScript
        const empresasDatos = <?php 
            $empresasData = [];
            foreach(findAllEmpresas() as $e) {
                if($e->getId() != $_SESSION['empresa']['id']) {
                    $empresasData[$e->getId()] = [
                        'id' => $e->getId(),
                        'nombre' => $e->getNombre(),
                        'sector' => $e->getSector(),
                        'descripcion' => $e->getDescripcion(),
                        'ciudad' => $e->getCiudad(),
                        'pais' => $e->getPais(),
                        'email' => $e->getEmail(),
                        'telefono' => $e->getTelefono(),
                        'sitio_web' => $e->getSitioWeb()
                    ];
                }
            }
            echo json_encode($empresasData);
        ?>;
    </script>
        <?php include_once '../includes/footer_alerts.php'; ?>

</body>
</html>