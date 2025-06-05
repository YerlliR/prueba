document.addEventListener('DOMContentLoaded', function() {
    const initSolicitudes = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initSolicitudes, 100);
            return;
        }
        
        const btnMisSolicitudes = document.getElementById('btn-mis-solicitudes');
        const modalSolicitudes = document.getElementById('modal-solicitudes');
        const tabButtons = document.querySelectorAll('.solicitud-tab-btn');
        const closeButtons = document.querySelectorAll('.modal-close');
        
        // Abrir modal
        if (btnMisSolicitudes) {
            btnMisSolicitudes.addEventListener('click', function() {
                modalSolicitudes.classList.add('active');
                document.body.style.overflow = 'hidden';
                
                showAlert({
                    type: 'info',
                    title: 'Cargando solicitudes',
                    message: 'Obteniendo tus solicitudes más recientes...',
                    duration: 2000
                });
                
                loadSolicitudes('enviadas');
            });
        }
        
        // Cambio de tabs
        tabButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const tabType = this.dataset.tab;
                
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                document.querySelectorAll('.solicitud-tab-content').forEach(content => {
                    content.classList.remove('active');
                });
                document.getElementById(`tab-${tabType}`).classList.add('active');
                
                showAlert({
                    type: 'info',
                    title: `Solicitudes ${tabType}`,
                    message: `Cargando solicitudes ${tabType}...`,
                    duration: 2000
                });
                
                loadSolicitudes(tabType);
            });
        });
        
        // Cerrar modal
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                modalSolicitudes.classList.remove('active');
                document.body.style.overflow = 'auto';
                
                showAlert({
                    type: 'info',
                    title: 'Modal cerrado',
                    message: 'Panel de solicitudes cerrado',
                    duration: 2000
                });
            });
        });
        
        modalSolicitudes.addEventListener('click', function(e) {
            if (e.target === modalSolicitudes) {
                modalSolicitudes.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        });
        
        // Cargar solicitudes
        function loadSolicitudes(type) {
            const container = document.getElementById(`solicitudes-${type}-container`);
            container.innerHTML = '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Cargando...</div>';
            
            fetch(`../../php/actions/obtenerSolicitudes.php?tipo=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        renderSolicitudes(container, data.solicitudes, type);
                        
                        const cantidad = data.solicitudes ? data.solicitudes.length : 0;
                        if (cantidad > 0) {
                            showAlert({
                                type: 'success',
                                title: 'Solicitudes cargadas',
                                message: `Se encontraron ${cantidad} solicitudes ${type}`,
                                duration: 3000
                            });
                        }
                    } else {
                        container.innerHTML = `<div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <p>${data.mensaje || 'No se pudieron cargar las solicitudes'}</p>
                        </div>`;
                        
                        showAlert({
                            type: 'warning',
                            title: 'Sin solicitudes',
                            message: `No tienes solicitudes ${type} en este momento`
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    container.innerHTML = `<div class="empty-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Error al cargar las solicitudes</p>
                        <button class="btn btn-secondary" onclick="loadSolicitudes('${type}')">Reintentar</button>
                    </div>`;
                    
                    showAlert({
                        type: 'error',
                        title: 'Error de conexión',
                        message: 'No se pudieron cargar las solicitudes. Verifica tu conexión.'
                    });
                });
        }
        
        // Renderizar solicitudes
        function renderSolicitudes(container, solicitudes, type) {
            if (!solicitudes || solicitudes.length === 0) {
                const tipoTexto = type === 'enviadas' ? 'enviadas' : 'recibidas';
                container.innerHTML = `<div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No tienes solicitudes ${tipoTexto} actualmente</p>
                    ${type === 'enviadas' ? '<p style="color: var(--text-light); font-size: 14px;">Explora proveedores para enviar tu primera solicitud</p>' : ''}
                </div>`;
                return;
            }
            
            let html = '<div class="solicitudes-list">';
            
            solicitudes.forEach(solicitud => {
                const isEnviada = type === 'enviadas';
                const empresaNombre = isEnviada ? solicitud.empresa_proveedor : solicitud.empresa_solicitante;
                const iniciales = getInitials(empresaNombre);
                const estadoClase = `estado-${solicitud.estado}`;
                const estadoTexto = capitalizeFirst(solicitud.estado);
                
                html += `
                    <div class="solicitud-card" data-id="${solicitud.id}">
                        <div class="solicitud-header">
                            <div class="solicitud-empresa">
                                <div class="solicitud-empresa-avatar">${iniciales}</div>
                                <div class="solicitud-empresa-info">
                                    <div class="solicitud-empresa-nombre">${empresaNombre}</div>
                                    <div class="solicitud-empresa-tipo">${isEnviada ? 'Proveedor' : 'Solicitante'}</div>
                                </div>
                            </div>
                            <div class="solicitud-meta">
                                <div class="solicitud-fecha">${formatDate(solicitud.fecha_creacion)}</div>
                                <div class="solicitud-estado ${estadoClase}">${estadoTexto}</div>
                            </div>
                        </div>
                        <div class="solicitud-body">
                            <div class="solicitud-asunto">${solicitud.asunto}</div>
                            <div class="solicitud-mensaje">${truncateText(solicitud.mensaje, 150)}</div>
                        </div>
                        <div class="solicitud-footer">`;
                
                // Botones de acción solo para solicitudes recibidas pendientes
                if (!isEnviada && solicitud.estado === 'pendiente') {
                    html += `
                            <div class="solicitud-acciones">
                                <button class="btn-solicitud btn-aceptar" data-action="aceptar" data-id="${solicitud.id}">
                                    <i class="fas fa-check"></i> Aceptar
                                </button>
                                <button class="btn-solicitud btn-rechazar" data-action="rechazar" data-id="${solicitud.id}">
                                    <i class="fas fa-times"></i> Rechazar
                                </button>
                            </div>`;
                } else {
                    html += `<div class="solicitud-info">
                        <small class="text-muted">${getSolicitudStatusText(solicitud.estado, isEnviada)}</small>
                    </div>`;
                }
                
                html += `</div></div>`;
            });
            
            html += '</div>';
            container.innerHTML = html;
            
            // Event listeners para botones de acción
            const actionButtons = container.querySelectorAll('.btn-solicitud');
            actionButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const solicitudId = this.dataset.id;
                    const action = this.dataset.action;
                    const solicitudCard = this.closest('.solicitud-card');
                    const empresaNombre = solicitudCard.querySelector('.solicitud-empresa-nombre').textContent;
                    
                    updateSolicitudEstado(solicitudId, action, empresaNombre);
                });
            });
        }
        
        // Actualizar estado de solicitud
        function updateSolicitudEstado(id, action, empresaNombre) {
            const actionText = action === 'aceptar' ? 'aceptar' : 'rechazar';
            const actionColor = action === 'aceptar' ? 'success' : 'warning';
            
            // Mostrar confirmación personalizada
            mostrarConfirmacionAccion(
                `¿Estás seguro de que deseas ${actionText} la solicitud de "${empresaNombre}"?`,
                action === 'aceptar' ? 
                    'Al aceptar, se establecerá una relación comercial y podrán intercambiar pedidos.' :
                    'Al rechazar, el solicitante será notificado de tu decisión.',
                actionColor
            ).then(confirmado => {
                if (confirmado) {
                    // Mostrar alerta de procesamiento
                    const processingId = showAlert({
                        type: 'loading',
                        title: `${action === 'aceptar' ? 'Aceptando' : 'Rechazando'} solicitud...`,
                        message: `Procesando solicitud de "${empresaNombre}"`,
                        persistent: true
                    });
                    
                    fetch('../../php/actions/actualizarSolicitud.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `id=${id}&accion=${action}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideAlert(processingId);
                        
                        if (data.success) {
                            showAlert({
                                type: action === 'aceptar' ? 'success' : 'info',
                                title: `Solicitud ${action === 'aceptar' ? 'aceptada' : 'rechazada'}`,
                                message: data.message || `Solicitud de "${empresaNombre}" ${action === 'aceptar' ? 'aceptada' : 'rechazada'} correctamente`
                            });
                            
                            const activeTab = document.querySelector('.solicitud-tab-btn.active').dataset.tab;
                            loadSolicitudes(activeTab);
                            
                            if (action === 'aceptar') {
                                setTimeout(() => {
                                    showAlert({
                                        type: 'info',
                                        title: 'Relación establecida',
                                        message: `"${empresaNombre}" ahora aparecerá en tu lista de clientes`,
                                        duration: 5000
                                    });
                                }, 2000);
                            }
                        } else {
                            showAlert({
                                type: 'error',
                                title: 'Error al procesar',
                                message: data.message || 'No se pudo procesar la solicitud'
                            });
                        }
                    })
                    .catch(error => {
                        hideAlert(processingId);
                        console.error('Error:', error);
                        showAlert({
                            type: 'error',
                            title: 'Error de conexión',
                            message: 'No se pudo conectar con el servidor. Inténtalo de nuevo.'
                        });
                    });
                }
            });
        }
        
        // Función para mostrar confirmación de acción
        function mostrarConfirmacionAccion(titulo, mensaje, tipo) {
            return new Promise((resolve) => {
                const tipoColor = tipo === 'success' ? '#059669' : tipo === 'warning' ? '#d97706' : '#dc2626';
                const tipoIcon = tipo === 'success' ? 'fa-check-circle' : tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle';
                
                const modalHtml = `
                    <div class="modal active" id="modal-confirmar-accion-solicitud" style="z-index: 10002;">
                        <div class="modal-content" style="max-width: 450px;">
                            <div class="modal-header">
                                <h2 style="color: ${tipoColor}; display: flex; align-items: center; gap: 10px;">
                                    <i class="fas ${tipoIcon}"></i>
                                    Confirmar acción
                                </h2>
                            </div>
                            <div class="modal-body">
                                <p style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">${titulo}</p>
                                <p style="color: var(--text-light); line-height: 1.5;">${mensaje}</p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" id="btn-cancelar-accion-solicitud">Cancelar</button>
                                <button class="btn" id="btn-confirmar-accion-solicitud" style="background: ${tipoColor}; color: white;">
                                    <i class="fas ${tipoIcon}"></i> Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                const modalAccion = document.getElementById('modal-confirmar-accion-solicitud');
                const btnCancelar = document.getElementById('btn-cancelar-accion-solicitud');
                const btnConfirmar = document.getElementById('btn-confirmar-accion-solicitud');
                
                const cerrarModalAccion = (resultado) => {
                    modalAccion.classList.remove('active');
                    setTimeout(() => modalAccion.remove(), 300);
                    resolve(resultado);
                };
                
                btnCancelar.addEventListener('click', () => cerrarModalAccion(false));
                btnConfirmar.addEventListener('click', () => cerrarModalAccion(true));
                
                modalAccion.addEventListener('click', (e) => {
                    if (e.target === modalAccion) cerrarModalAccion(false);
                });
            });
        }
        
        // Funciones auxiliares
        function getInitials(name) {
            if (!name) return '??';
            return name.split(' ')
                .map(word => word.charAt(0))
                .join('')
                .substring(0, 2)
                .toUpperCase();
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffTime = Math.abs(now - date);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays === 1) {
                return 'Hace 1 día';
            } else if (diffDays < 7) {
                return `Hace ${diffDays} días`;
            } else {
                return date.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }
        }
        
        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        function truncateText(text, maxLength) {
            if (text.length <= maxLength) return text;
            return text.substring(0, maxLength) + '...';
        }
        
        function getSolicitudStatusText(estado, isEnviada) {
            if (estado === 'pendiente') {
                return isEnviada ? 'Esperando respuesta del proveedor' : 'Pendiente de respuesta';
            } else if (estado === 'aceptada') {
                return isEnviada ? 'Solicitud aceptada - Relación establecida' : 'Solicitud aceptada';
            } else if (estado === 'rechazada') {
                return isEnviada ? 'Solicitud rechazada' : 'Solicitud rechazada';
            }
            return '';
        }
        
        window.loadSolicitudes = loadSolicitudes;
        console.log('Sistema de solicitudes inicializado correctamente');
    };
    
    initSolicitudes();
});