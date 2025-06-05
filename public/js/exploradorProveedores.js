// ===== ARCHIVO: public/js/exploradorProveedores.js - SIN ALERTAS DE ENTRADA =====
// Sistema completo para el explorador de proveedores

document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let selectedProveedorID = null;
    let currentProveedorNombre = '';
    
    // Referencias a elementos del DOM
    const modalContact = document.getElementById('modal-contact');
    const btnSendModalMessage = document.getElementById('btn-send-modal-message');
    const btnCancelContact = document.getElementById('btn-cancel-contact');
    const contactProviderName = document.getElementById('contact-provider-name');
    
    // Función para inicializar después de que el sistema de alertas esté disponible
    const initContactSystem = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initContactSystem, 100);
            return;
        }
        
        console.log('Sistema de explorador de proveedores inicializado con alertas');
        
        // ===== GESTIONAR CONTACTO CON PROVEEDORES =====
        
        // Asignar eventos a botones de contacto (delegación de eventos)
        document.addEventListener('click', function(e) {
            // Botón "Contactar" en las tarjetas de proveedor
            if (e.target.closest('.btn-contact')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.closest('.btn-contact');
                selectedProveedorID = btn.getAttribute('data-empresa-id');
                
                // Obtener el nombre del proveedor desde la tarjeta
                const providerCard = btn.closest('.provider-card');
                currentProveedorNombre = providerCard ? 
                    providerCard.querySelector('h3').textContent.trim() : 'Proveedor';
                
                console.log('Contactar proveedor:', selectedProveedorID, currentProveedorNombre);
                abrirModalContacto();
            }
            
            // Botón "Ver perfil" en las tarjetas de proveedor
            if (e.target.closest('.btn-view-profile')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.closest('.btn-view-profile');
                const empresaId = btn.getAttribute('data-empresa-id');
                mostrarPerfilEmpresa(empresaId);
            }
            
            // Botón "Solicitar servicio" desde el perfil
            if (e.target.classList.contains('btn-solicitar-desde-perfil')) {
                e.preventDefault();
                
                selectedProveedorID = e.target.getAttribute('data-empresa-id');
                
                // Obtener nombre del proveedor desde el perfil
                const perfilNombre = document.getElementById('perfil-nombre');
                currentProveedorNombre = perfilNombre ? 
                    perfilNombre.textContent.trim() : 'Proveedor';
                
                // Cerrar modal de perfil si está abierto
                const modalPerfil = document.getElementById('modal-perfil');
                if (modalPerfil) {
                    modalPerfil.classList.remove('active');
                }
                
                abrirModalContacto();
            }
        });
        
        // ===== FUNCIÓN PARA MOSTRAR PERFIL DE EMPRESA =====
        function mostrarPerfilEmpresa(empresaId) {
            const empresa = empresasDatos[empresaId];
            if (!empresa) {
                showAlert({
                    type: 'error',
                    title: 'Error',
                    message: 'No se pudo cargar la información de la empresa'
                });
                return;
            }
            
            const iniciales = empresa.nombre.substring(0, 2).toUpperCase();
            
            const modalHtml = `
                <div class="modal active" id="modal-perfil-empresa">
                    <div class="modal-content" style="max-width: 700px;">
                        <div class="modal-header">
                            <h2 id="perfil-nombre">${empresa.nombre}</h2>
                            <button class="modal-close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="perfil-header" style="display: flex; gap: 20px; margin-bottom: 25px;">
                                <div class="perfil-avatar" style="width: 80px; height: 80px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; color: white; font-size: 32px; font-weight: 600;">
                                    ${iniciales}
                                </div>
                                <div class="perfil-info" style="flex: 1;">
                                    <h3 style="margin: 0 0 10px 0; color: var(--text-color);">${empresa.nombre}</h3>
                                    <div style="display: flex; gap: 15px; margin-bottom: 10px; flex-wrap: wrap;">
                                        <span style="display: flex; align-items: center; gap: 5px; color: var(--text-light);">
                                            <i class="fas fa-tag"></i> ${empresa.sector}
                                        </span>
                                        <span style="display: flex; align-items: center; gap: 5px; color: var(--text-light);">
                                            <i class="fas fa-map-marker-alt"></i> ${empresa.ciudad}, ${empresa.pais}
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 5px;">
                                        <div style="display: flex; color: #f59e0b;">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star-half-alt"></i>
                                        </div>
                                        <span style="margin-left: 5px; font-weight: 600;">4.5</span>
                                        <span style="color: var(--text-light); margin-left: 5px;">(24 reseñas)</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div style="margin-bottom: 20px;">
                                <h4 style="color: var(--primary-color); margin-bottom: 10px;">Descripción</h4>
                                <p style="color: var(--text-light); line-height: 1.6;">${empresa.descripcion || 'Sin descripción disponible'}</p>
                            </div>
                            
                            <div style="border-top: 1px solid var(--border-color); padding-top: 20px;">
                                <h4 style="color: var(--primary-color); margin-bottom: 15px;">Información de contacto</h4>
                                <div style="display: flex; flex-direction: column; gap: 15px;">
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <span>${empresa.email || 'No disponible'}</span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <span>${empresa.telefono || 'No disponible'}</span>
                                    </div>
                                    ${empresa.sitio_web ? `
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <div style="width: 40px; height: 40px; background: var(--bg-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--primary-color);">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <a href="${empresa.sitio_web}" target="_blank" style="color: var(--primary-color); text-decoration: none;">${empresa.sitio_web}</a>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary modal-close">Cerrar</button>
                            <button class="btn btn-primary btn-solicitar-desde-perfil" data-empresa-id="${empresa.id}">
                                <i class="fas fa-paper-plane"></i> Contactar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Eliminar modal anterior si existe
            const modalAnterior = document.getElementById('modal-perfil-empresa');
            if (modalAnterior) modalAnterior.remove();
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
            
            const modal = document.getElementById('modal-perfil-empresa');
            modal.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    setTimeout(() => modal.remove(), 300);
                });
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    setTimeout(() => modal.remove(), 300);
                }
            });
        }
        
        // ===== FUNCIÓN PARA ABRIR MODAL DE CONTACTO =====
        function abrirModalContacto() {
            if (!selectedProveedorID) {
                showAlert({
                    type: 'error',
                    title: 'Error',
                    message: 'No se ha seleccionado un proveedor'
                });
                return;
            }
            
            // Actualizar el nombre en el modal
            if (contactProviderName) {
                contactProviderName.textContent = currentProveedorNombre;
            }
            
            // Limpiar campos del formulario
            limpiarFormularioContacto();
            
            // Mostrar el modal
            if (modalContact) {
                modalContact.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                // Enfocar el primer campo
                const firstInput = modalContact.querySelector('#modal-contact-subject');
                if (firstInput) {
                    setTimeout(() => firstInput.focus(), 300);
                }
            }
        }
        
        // ===== FUNCIÓN PARA LIMPIAR FORMULARIO =====
        function limpiarFormularioContacto() {
            const subjectInput = document.getElementById('modal-contact-subject');
            const messageInput = document.getElementById('modal-contact-message');
            const messageCounter = document.getElementById('message-counter');
            
            if (subjectInput) subjectInput.value = '';
            if (messageInput) messageInput.value = '';
            if (messageCounter) messageCounter.textContent = '0';
            
            // Limpiar mensajes de error si existen
            const errorMessages = modalContact?.querySelectorAll('.error-message') || [];
            errorMessages.forEach(error => error.remove());
            
            // Limpiar estilos de error
            const inputs = modalContact?.querySelectorAll('input, textarea') || [];
            inputs.forEach(input => {
                input.style.borderColor = '';
                input.classList.remove('error');
            });
        }
        
        // ===== ENVIAR FORMULARIO DE CONTACTO =====
        if (btnSendModalMessage) {
            btnSendModalMessage.addEventListener('click', function(e) {
                e.preventDefault();
                enviarSolicitud();
            });
        }
        
        // También permitir envío con Ctrl+Enter en el textarea
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey && modalContact?.classList.contains('active')) {
                e.preventDefault();
                enviarSolicitud();
            }
        });
        
        // ===== FUNCIÓN PRINCIPAL PARA ENVIAR SOLICITUD =====
        function enviarSolicitud() {
            console.log('Iniciando envío de solicitud...');
            
            // Verificar que se haya seleccionado un proveedor
            if (!selectedProveedorID) {
                showAlert({
                    type: 'error',
                    title: 'Error',
                    message: 'No se ha seleccionado un proveedor'
                });
                return;
            }
            
            // Obtener valores de los campos
            const subjectInput = document.getElementById('modal-contact-subject');
            const messageInput = document.getElementById('modal-contact-message');
            
            if (!subjectInput || !messageInput) {
                showAlert({
                    type: 'error',
                    title: 'Error',
                    message: 'No se pudieron encontrar los campos del formulario'
                });
                return;
            }
            
            const subject = subjectInput.value.trim();
            const message = messageInput.value.trim();
            
            console.log('Datos del formulario:', { subject, message, selectedProveedorID });
            
            // Validación del formulario
            const validationResult = validarFormularioContacto(subject, message);
            if (!validationResult.isValid) {
                showAlert({
                    type: 'warning',
                    title: 'Formulario incompleto',
                    message: validationResult.message
                });
                return;
            }
            
            // Deshabilitar botón para evitar envíos múltiples
            const originalText = btnSendModalMessage.innerHTML;
            btnSendModalMessage.disabled = true;
            btnSendModalMessage.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            
            // Mostrar alerta de carga
            const loadingId = showAlert({
                type: 'loading',
                title: 'Enviando solicitud...',
                message: 'Tu solicitud está siendo enviada al proveedor',
                persistent: true
            });
            
            // Datos para enviar al servidor
            const solicitudData = {
                id_empresa_proveedor: parseInt(selectedProveedorID),
                asunto: subject,
                mensaje: message
            };
            
            console.log('Enviando solicitud:', solicitudData);
            
            // Enviar solicitud al servidor
            fetch('../../php/actions/procesarSolicitud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(solicitudData)
            })
            .then(response => {
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text().then(text => {
                    console.log('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Response not JSON:', text);
                        throw new Error('Respuesta del servidor no válida: ' + text.substring(0, 100));
                    }
                });
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (loadingId && typeof hideAlert === 'function') {
                    hideAlert(loadingId);
                }
                
                if (data.success) {
                    // Éxito: mostrar mensaje y cerrar modal
                    showAlert({
                        type: 'success',
                        title: 'Solicitud enviada',
                        message: data.message || `Solicitud enviada correctamente a ${currentProveedorNombre}`
                    });
                    
                    cerrarModalContacto();
                    
                    // Opcional: mostrar información adicional
                    setTimeout(() => {
                        showAlert({
                            type: 'info',
                            title: 'Información',
                            message: 'Recibirás una notificación cuando el proveedor responda a tu solicitud'
                        });
                    }, 2000);
                    
                } else {
                    // Error del servidor
                    showAlert({
                        type: 'error',
                        title: 'Error al enviar',
                        message: data.message || 'Error al enviar la solicitud'
                    });
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                
                if (loadingId && typeof hideAlert === 'function') {
                    hideAlert(loadingId);
                }
                
                showAlert({
                    type: 'error',
                    title: 'Error de conexión',
                    message: 'No se pudo conectar con el servidor. Verifica tu conexión a internet e inténtalo de nuevo.'
                });
            })
            .finally(() => {
                // Restablecer botón
                btnSendModalMessage.disabled = false;
                btnSendModalMessage.innerHTML = originalText;
            });
        }
        
        // ===== FUNCIÓN DE VALIDACIÓN =====
        function validarFormularioContacto(subject, message) {
            // Limpiar mensajes de error previos
            const errorMessages = modalContact?.querySelectorAll('.error-message') || [];
            errorMessages.forEach(error => error.remove());
            
            const errors = [];
            
            // Validar asunto
            if (!subject) {
                errors.push({ field: 'modal-contact-subject', message: 'El asunto es obligatorio' });
            } else if (subject.length < 5) {
                errors.push({ field: 'modal-contact-subject', message: 'El asunto debe tener al menos 5 caracteres' });
            } else if (subject.length > 100) {
                errors.push({ field: 'modal-contact-subject', message: 'El asunto no puede exceder 100 caracteres' });
            }
            
            // Validar mensaje
            if (!message) {
                errors.push({ field: 'modal-contact-message', message: 'El mensaje es obligatorio' });
            } else if (message.length < 20) {
                errors.push({ field: 'modal-contact-message', message: 'El mensaje debe tener al menos 20 caracteres' });
            } else if (message.length > 1000) {
                errors.push({ field: 'modal-contact-message', message: 'El mensaje no puede exceder 1000 caracteres' });
            }
            
            // Mostrar errores en el formulario
            if (errors.length > 0) {
                errors.forEach(error => {
                    const field = document.getElementById(error.field);
                    if (field) {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message';
                        errorDiv.style.cssText = 'color: #dc2626; font-size: 12px; margin-top: 4px;';
                        errorDiv.textContent = error.message;
                        
                        field.parentNode.appendChild(errorDiv);
                        field.style.borderColor = '#dc2626';
                        field.classList.add('error');
                    }
                });
                
                return {
                    isValid: false,
                    message: errors[0].message
                };
            }
            
            return { isValid: true };
        }
        
        // ===== CERRAR MODAL DE CONTACTO =====
        function cerrarModalContacto() {
            if (modalContact) {
                modalContact.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
            
            // Limpiar datos
            setTimeout(() => {
                limpiarFormularioContacto();
                selectedProveedorID = null;
                currentProveedorNombre = '';
            }, 300);
        }
        
        // Event listeners para cerrar modal
        if (btnCancelContact) {
            btnCancelContact.addEventListener('click', cerrarModalContacto);
        }
        
        // Botones de cierre general
        const modalCloseButtons = modalContact?.querySelectorAll('.modal-close') || [];
        modalCloseButtons.forEach(btn => {
            btn.addEventListener('click', cerrarModalContacto);
        });
        
        // Cerrar modal al hacer clic fuera del contenido
        if (modalContact) {
            modalContact.addEventListener('click', function(e) {
                if (e.target === modalContact) {
                    cerrarModalContacto();
                }
            });
        }
        
        // Cerrar modal con Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modalContact?.classList.contains('active')) {
                cerrarModalContacto();
            }
        });
        
        // ===== CONTADOR DE CARACTERES =====
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
        const formInputs = modalContact?.querySelectorAll('input, textarea') || [];
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '';
                this.classList.remove('error');
                
                // Remover mensajes de error
                const errorMsg = this.parentNode.querySelector('.error-message');
                if (errorMsg) {
                    errorMsg.remove();
                }
            });
        });
        
        console.log('Sistema de solicitudes inicializado correctamente');
    };
    
    // Inicializar el sistema
    initContactSystem();
    
    // ===== FILTRADO DE CATEGORÍAS =====
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('click', function() {
            const category = this.dataset.category;
            if (category) {
                filtrarPorCategoria(category);
            }
        });
    });
    
    function filtrarPorCategoria(categoria) {
        const providerCards = document.querySelectorAll('.provider-card');
        let visibleCount = 0;
        
        providerCards.forEach(card => {
            const tags = card.querySelectorAll('.tag');
            let matches = false;
            
            tags.forEach(tag => {
                if (tag.classList.contains(`tag-${categoria.toLowerCase()}`)) {
                    matches = true;
                }
            });
            
            if (matches) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Actualizar título de categoría
        const categoryTitle = document.getElementById('category-title');
        if (categoryTitle) {
            categoryTitle.textContent = `- ${categoria.charAt(0).toUpperCase() + categoria.slice(1)}`;
        }
        
        // Actualizar contador de resultados
        const resultsInfo = document.querySelector('.results-info p');
        if (resultsInfo) {
            resultsInfo.textContent = `Mostrando ${visibleCount} resultados para ${categoria}`;
        }
        
        // Scroll suave hasta la sección de resultados
        const providersSection = document.querySelector('.providers-section');
        if (providersSection) {
            providersSection.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    // ===== RESETEAR FILTROS =====
    const resetButton = document.getElementById('reset-filters');
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            // Mostrar todas las tarjetas
            const providerCards = document.querySelectorAll('.provider-card');
            providerCards.forEach(card => {
                card.style.display = '';
            });
            
            // Limpiar filtros
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => cb.checked = false);
            
            // Resetear título
            const categoryTitle = document.getElementById('category-title');
            if (categoryTitle) {
                categoryTitle.textContent = '';
            }
            
            // Resetear contador
            const resultsInfo = document.querySelector('.results-info p');
            if (resultsInfo) {
                resultsInfo.textContent = 'Mostrando todos los resultados';
            }
            
            showAlert({
                type: 'info',
                title: 'Filtros restablecidos',
                message: 'Ahora se muestran todos los proveedores'
            });
        });
    }
    
    // ===== FILTROS DE UBICACIÓN =====
    const locationCheckboxes = document.querySelectorAll('input[name="location"]');
    locationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            aplicarFiltrosUbicacion();
        });
    });
    
    function aplicarFiltrosUbicacion() {
        const selectedLocations = Array.from(locationCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value.toLowerCase());
        
        const providerCards = document.querySelectorAll('.provider-card');
        let visibleCount = 0;
        
        providerCards.forEach(card => {
            if (selectedLocations.length === 0) {
                // Si no hay filtros seleccionados, mostrar todas
                card.style.display = '';
                visibleCount++;
            } else {
                // Verificar si la ubicación coincide
                const locationElement = card.querySelector('.provider-location');
                if (locationElement) {
                    const locationText = locationElement.textContent.toLowerCase();
                    const matches = selectedLocations.some(location => 
                        locationText.includes(location)
                    );
                    
                    if (matches) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                }
            }
        });
        
        // Actualizar contador de resultados
        const resultsInfo = document.querySelector('.results-info p');
        if (resultsInfo) {
            if (selectedLocations.length > 0) {
                resultsInfo.textContent = `Mostrando ${visibleCount} resultados para ${selectedLocations.join(', ')}`;
            } else {
                resultsInfo.textContent = 'Mostrando todos los resultados';
            }
        }
    }
    
    // ===== BÚSQUEDA EN TIEMPO REAL =====
    const heroSearch = document.getElementById('hero-search');
    if (heroSearch) {
        let searchTimeout;
        
        heroSearch.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim().toLowerCase();
            
            searchTimeout = setTimeout(() => {
                buscarProveedores(query);
            }, 300); // Esperar 300ms antes de buscar
        });
    }
    
    function buscarProveedores(query) {
        const providerCards = document.querySelectorAll('.provider-card');
        let visibleCount = 0;
        
        if (!query) {
            // Si no hay búsqueda, mostrar todos
            providerCards.forEach(card => {
                card.style.display = '';
                visibleCount++;
            });
        } else {
            providerCards.forEach(card => {
                const nombre = card.querySelector('h3').textContent.toLowerCase();
                const descripcion = card.querySelector('.provider-description').textContent.toLowerCase();
                const ubicacion = card.querySelector('.provider-location').textContent.toLowerCase();
                const sector = card.querySelector('.tag').textContent.toLowerCase();
                
                if (nombre.includes(query) || 
                    descripcion.includes(query) || 
                    ubicacion.includes(query) || 
                    sector.includes(query)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        // Actualizar contador de resultados
        const resultsInfo = document.querySelector('.results-info p');
        if (resultsInfo) {
            if (query) {
                resultsInfo.textContent = `Mostrando ${visibleCount} resultados para "${query}"`;
            } else {
                resultsInfo.textContent = 'Mostrando todos los resultados';
            }
        }
    }
    
    // ===== VERIFICAR CONEXIÓN =====
    function verificarConexion() {
        if (!navigator.onLine) {
            if (typeof showAlert === 'function') {
                showAlert({
                    type: 'warning',
                    title: 'Sin conexión',
                    message: 'No tienes conexión a internet. Algunas funciones pueden no estar disponibles.',
                    persistent: true
                });
            }
        }
    }
    
    // Event listeners para estado de conexión
    window.addEventListener('online', () => {
        if (typeof showAlert === 'function') {
            showAlert({
                type: 'success',
                title: 'Conexión restaurada',
                message: 'Tu conexión a internet se ha restablecido'
            });
        }
    });
    
    window.addEventListener('offline', () => {
        verificarConexion();
    });
    
    // Verificar conexión inicial
    verificarConexion();
});