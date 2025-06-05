document.addEventListener('DOMContentLoaded', function() {
    const initPedidosVista = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initPedidosVista, 100);
            return;
        }
        
        // Función global para ver detalle de pedido
        window.verDetallePedido = function(pedidoId) {
            const loadingId = showAlert({
                type: 'loading',
                title: 'Cargando detalle...',
                message: 'Obteniendo información completa del pedido',
                persistent: true
            });
            
            fetch(`../../php/actions/obtenerDetallePedido.php?id=${pedidoId}`)
                .then(response => response.json())
                .then(data => {
                    hideAlert(loadingId);
                    
                    if (data.success) {
                        mostrarDetallePedido(data.pedido);
                        showAlert({
                            type: 'success',
                            title: 'Detalle cargado',
                            message: `Información del pedido #${data.pedido.numero_pedido} cargada correctamente`,
                            duration: 3000
                        });
                    } else {
                        showAlert({
                            type: 'error',
                            title: 'Error al cargar detalle',
                            message: data.mensaje || 'No se pudo cargar el detalle del pedido'
                        });
                    }
                })
                .catch(error => {
                    hideAlert(loadingId);
                    console.error('Error:', error);
                    showAlert({
                        type: 'error',
                        title: 'Error de conexión',
                        message: 'No se pudo conectar con el servidor. Verifica tu conexión.'
                    });
                });
        };
        
        // Función global para cambiar estado de pedido
        window.cambiarEstadoPedido = function(pedidoId, nuevoEstado) {
            const estadosTexto = {
                'procesando': 'procesar',
                'completado': 'completar',
                'cancelado': 'cancelar'
            };
            
            const estadosColores = {
                'procesando': 'info',
                'completado': 'success',
                'cancelado': 'warning'
            };
            
            const accion = estadosTexto[nuevoEstado] || 'actualizar';
            const color = estadosColores[nuevoEstado] || 'info';
            
            // Mostrar confirmación personalizada
            mostrarConfirmacionCambioEstado(
                `¿Estás seguro de que deseas ${accion} este pedido?`,
                getDescripcionCambioEstado(nuevoEstado),
                color
            ).then(confirmado => {
                if (confirmado) {
                    const loadingId = showAlert({
                        type: 'loading',
                        title: `${accion.charAt(0).toUpperCase() + accion.slice(1)}ando pedido...`,
                        message: `Actualizando estado del pedido`,
                        persistent: true
                    });
                    
                    fetch('../../php/actions/actualizarEstadoPedido.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            pedidoId: pedidoId,
                            estado: nuevoEstado
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideAlert(loadingId);
                        
                        if (data.success) {
                            showAlert({
                                type: color,
                                title: 'Estado actualizado',
                                message: data.message || `El pedido ha sido ${accion}do correctamente`,
                                duration: 4000
                            });
                            
                            actualizarEstadoEnUI(pedidoId, nuevoEstado);
                            
                            // Mostrar información adicional según el estado
                            if (nuevoEstado === 'completado') {
                                setTimeout(() => {
                                    showAlert({
                                        type: 'info',
                                        title: 'Pedido completado',
                                        message: 'El cliente será notificado de que su pedido está listo',
                                        duration: 4000
                                    });
                                }, 2000);
                            } else if (nuevoEstado === 'procesando') {
                                setTimeout(() => {
                                    showAlert({
                                        type: 'info',
                                        title: 'Pedido en proceso',
                                        message: 'El cliente ha sido notificado del cambio de estado',
                                        duration: 4000
                                    });
                                }, 2000);
                            }
                        } else {
                            showAlert({
                                type: 'error',
                                title: 'Error al actualizar',
                                message: data.message || 'No se pudo actualizar el estado del pedido'
                            });
                        }
                    })
                    .catch(error => {
                        hideAlert(loadingId);
                        console.error('Error:', error);
                        showAlert({
                            type: 'error',
                            title: 'Error de conexión',
                            message: 'No se pudo conectar con el servidor. Inténtalo de nuevo.'
                        });
                    });
                }
            });
        };
        
        // Función para mostrar confirmación de cambio de estado
        function mostrarConfirmacionCambioEstado(titulo, descripcion, tipo) {
            return new Promise((resolve) => {
                const tipoColor = tipo === 'success' ? '#059669' : tipo === 'warning' ? '#d97706' : '#3b82f6';
                const tipoIcon = tipo === 'success' ? 'fa-check-circle' : tipo === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
                
                const modalHtml = `
                    <div class="modal active" id="modal-confirmar-estado-pedido" style="z-index: 10001;">
                        <div class="modal-content" style="max-width: 450px;">
                            <div class="modal-header">
                                <h2 style="color: ${tipoColor}; display: flex; align-items: center; gap: 10px;">
                                    <i class="fas ${tipoIcon}"></i>
                                    Confirmar cambio de estado
                                </h2>
                            </div>
                            <div class="modal-body">
                                <p style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">${titulo}</p>
                                <p style="color: var(--text-light); line-height: 1.5;">${descripcion}</p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" id="btn-cancelar-estado-pedido">Cancelar</button>
                                <button class="btn" id="btn-confirmar-estado-pedido" style="background: ${tipoColor}; color: white;">
                                    <i class="fas ${tipoIcon}"></i> Confirmar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                
                const modalEstado = document.getElementById('modal-confirmar-estado-pedido');
                const btnCancelar = document.getElementById('btn-cancelar-estado-pedido');
                const btnConfirmar = document.getElementById('btn-confirmar-estado-pedido');
                
                const cerrarModalEstado = (resultado) => {
                    modalEstado.classList.remove('active');
                    setTimeout(() => modalEstado.remove(), 300);
                    resolve(resultado);
                };
                
                btnCancelar.addEventListener('click', () => cerrarModalEstado(false));
                btnConfirmar.addEventListener('click', () => cerrarModalEstado(true));
                
                modalEstado.addEventListener('click', (e) => {
                    if (e.target === modalEstado) cerrarModalEstado(false);
                });
            });
        }
        
        // Función para obtener descripción del cambio de estado
        function getDescripcionCambioEstado(estado) {
            const descripciones = {
                'procesando': 'El pedido pasará a estado "En Proceso" y el cliente será notificado.',
                'completado': 'El pedido se marcará como completado y el cliente será notificado.',
                'cancelado': 'El pedido será cancelado. Esta acción no se puede deshacer.'
            };
            return descripciones[estado] || 'Se actualizará el estado del pedido.';
        }
        
        function mostrarDetallePedido(pedido) {
            const modalHtml = `
                <div id="modal-detalle-pedido" class="modal active">
                    <div class="modal-content modal-detalle-content">
                        <div class="modal-header">
                            <h2>Detalle del Pedido #${pedido.numero_pedido}</h2>
                            <button class="modal-close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="pedido-info-header">
                                <div class="info-section">
                                    <h3>Información General</h3>
                                    <p><strong>Empresa ${pedido.tipo === 'recibido' ? 'Cliente' : 'Proveedor'}:</strong> ${pedido.nombre_empresa}</p>
                                    <p><strong>Email:</strong> ${pedido.email_empresa}</p>
                                    <p><strong>Teléfono:</strong> ${pedido.telefono_empresa}</p>
                                    <p><strong>Fecha del pedido:</strong> ${new Date(pedido.fecha_pedido).toLocaleDateString()}</p>
                                    ${pedido.fecha_entrega_estimada ? `<p><strong>Entrega estimada:</strong> ${new Date(pedido.fecha_entrega_estimada).toLocaleDateString()}</p>` : ''}
                                    ${pedido.direccion_entrega ? `<p><strong>Dirección de entrega:</strong> ${pedido.direccion_entrega}</p>` : ''}
                                    ${pedido.notas ? `<p><strong>Notas:</strong> ${pedido.notas}</p>` : ''}
                                </div>
                                <div class="estado-section">
                                    <div class="pedido-estado estado-${pedido.estado}">
                                        ${pedido.estado.charAt(0).toUpperCase() + pedido.estado.slice(1)}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pedido-productos">
                                <h3>Productos del Pedido</h3>
                                <div class="productos-tabla">
                                    <div class="tabla-header">
                                        <div>Producto</div>
                                        <div>Cantidad</div>
                                        <div>Precio Unit.</div>
                                        <div>IVA</div>
                                        <div>Total</div>
                                    </div>
                                    ${pedido.lineas.map(linea => `
                                        <div class="tabla-row">
                                            <div class="producto-nombre">${linea.nombre_producto}</div>
                                            <div>${linea.cantidad}</div>
                                            <div>${parseFloat(linea.precio_unitario).toFixed(2)} €</div>
                                            <div>${linea.iva}%</div>
                                            <div>${parseFloat(linea.total).toFixed(2)} €</div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                            
                            <div class="pedido-totales">
                                <div class="total-row">
                                    <span>Subtotal:</span>
                                    <span>${parseFloat(pedido.subtotal).toFixed(2)} €</span>
                                </div>
                                <div class="total-row">
                                    <span>IVA:</span>
                                    <span>${parseFloat(pedido.total_iva).toFixed(2)} €</span>
                                </div>
                                <div class="total-row total-final">
                                    <span>Total:</span>
                                    <span>${parseFloat(pedido.total).toFixed(2)} €</span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary modal-close">Cerrar</button>
                        </div>
                    </div>
                </div>
            `;
            
            const modalAnterior = document.getElementById('modal-detalle-pedido');
            if (modalAnterior) {
                modalAnterior.remove();
            }
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
            
            const modal = document.getElementById('modal-detalle-pedido');
            modal.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    setTimeout(() => modal.remove(), 300);
                    
                    showAlert({
                        type: 'info',
                        title: 'Detalle cerrado',
                        message: 'Ventana de detalle del pedido cerrada',
                        duration: 2000
                    });
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
        
        function actualizarEstadoEnUI(pedidoId, nuevoEstado) {
            const pedidoCard = document.querySelector(`[data-pedido-id="${pedidoId}"]`);
            if (pedidoCard) {
                const estadoElement = pedidoCard.querySelector('.pedido-estado');
                if (estadoElement) {
                    estadoElement.className = `pedido-estado estado-${nuevoEstado}`;
                    estadoElement.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
                }
                
                const footer = pedidoCard.querySelector('.pedido-footer');
                if (footer) {
                    actualizarBotonesPedido(footer, nuevoEstado, pedidoId);
                }
                
                // Añadir efecto visual de actualización
                pedidoCard.style.background = 'rgba(59, 130, 246, 0.1)';
                pedidoCard.style.transition = 'background 1s ease';
                setTimeout(() => {
                    pedidoCard.style.background = '';
                }, 1000);
            }
        }
        
        function actualizarBotonesPedido(footer, estado, pedidoId) {
            const botonesAccion = footer.querySelectorAll('.btn-procesar, .btn-completar, .btn-cancelar');
            botonesAccion.forEach(btn => btn.remove());
            
            const btnVerDetalle = footer.querySelector('.btn-ver-detalle');
            
            if (estado === 'pendiente') {
                if (window.location.pathname.includes('recibidos')) {
                    btnVerDetalle.insertAdjacentHTML('afterend', `
                        <button class="btn-pedido btn-procesar" onclick="cambiarEstadoPedido(${pedidoId}, 'procesando')" title="Marcar como en proceso">
                            <i class="fas fa-cog"></i> Procesar
                        </button>
                    `);
                } else if (window.location.pathname.includes('enviados')) {
                    btnVerDetalle.insertAdjacentHTML('afterend', `
                        <button class="btn-pedido btn-cancelar" onclick="cambiarEstadoPedido(${pedidoId}, 'cancelado')" title="Cancelar pedido">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                    `);
                }
            } else if (estado === 'procesando' && window.location.pathname.includes('recibidos')) {
                btnVerDetalle.insertAdjacentHTML('afterend', `
                    <button class="btn-pedido btn-completar" onclick="cambiarEstadoPedido(${pedidoId}, 'completado')" title="Marcar como completado">
                        <i class="fas fa-check"></i> Completar
                    </button>
                `);
            }
        }
        
        // Filtros mejorados con alertas
        const searchInput = document.querySelector('.search-input');
        const estadoFilter = document.getElementById('filter-estado');
        const fechaFilter = document.getElementById('filter-fecha');
        
        let filtroTimeout;
        
        function filtrarPedidos() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const estadoSeleccionado = estadoFilter?.value || '';
            const fechaSeleccionada = fechaFilter?.value || '';
            
            const pedidoCards = document.querySelectorAll('.pedido-card');
            let pedidosVisibles = 0;
            
            pedidoCards.forEach(card => {
                const numeroPedido = card.querySelector('.pedido-numero')?.textContent.toLowerCase() || '';
                const empresa = card.querySelector('.pedido-proveedor, .pedido-cliente')?.textContent.toLowerCase() || '';
                const estado = card.querySelector('.pedido-estado')?.textContent.toLowerCase() || '';
                const fechaPedido = card.querySelector('.detalle-item')?.textContent || '';
                
                let shouldShow = true;
                
                // Filtro de búsqueda
                if (searchTerm && !numeroPedido.includes(searchTerm) && !empresa.includes(searchTerm)) {
                    shouldShow = false;
                }
                
                // Filtro de estado
                if (estadoSeleccionado && !estado.includes(estadoSeleccionado)) {
                    shouldShow = false;
                }
                
                // Filtro de fecha (básico)
                if (fechaSeleccionada && fechaSeleccionada !== '') {
                    const hoy = new Date();
                    const fechaCard = new Date(fechaPedido.split(': ')[1]);
                    
                    switch (fechaSeleccionada) {
                        case 'hoy':
                            shouldShow = shouldShow && fechaCard.toDateString() === hoy.toDateString();
                            break;
                        case 'semana':
                            const hace7Dias = new Date(hoy.getTime() - 7 * 24 * 60 * 60 * 1000);
                            shouldShow = shouldShow && fechaCard >= hace7Dias;
                            break;
                        case 'mes':
                            const hace30Dias = new Date(hoy.getTime() - 30 * 24 * 60 * 60 * 1000);
                            shouldShow = shouldShow && fechaCard >= hace30Dias;
                            break;
                    }
                }
                
                if (shouldShow) {
                    card.style.display = '';
                    pedidosVisibles++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Mostrar resultado del filtrado
            clearTimeout(filtroTimeout);
            filtroTimeout = setTimeout(() => {
                if (searchTerm || estadoSeleccionado || fechaSeleccionada) {
                    showAlert({
                        type: 'info',
                        title: 'Filtros aplicados',
                        message: `Se encontraron ${pedidosVisibles} pedido(s) que coinciden con los criterios`,
                        duration: 3000
                    });
                }
            }, 500);
            
            // Mostrar mensaje si no hay resultados
            mostrarMensajeNoResultados(pedidosVisibles, { searchTerm, estadoSeleccionado, fechaSeleccionada });
        }
        
        function mostrarMensajeNoResultados(cantidad, filtros) {
            const container = document.querySelector('.pedidos-lista');
            let noResultsMessage = document.getElementById('no-results-pedidos');
            
            if (cantidad === 0 && (filtros.searchTerm || filtros.estadoSeleccionado || filtros.fechaSeleccionada)) {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.id = 'no-results-pedidos';
                    noResultsMessage.className = 'no-results-message';
                    noResultsMessage.style.cssText = `
                        text-align: center;
                        padding: 40px;
                        color: var(--text-light);
                        border: 2px dashed var(--border-color);
                        border-radius: var(--border-radius-lg);
                        margin: 20px 0;
                    `;
                    container.appendChild(noResultsMessage);
                }
                
                noResultsMessage.innerHTML = `
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>No se encontraron pedidos</h3>
                    <p>No hay pedidos que coincidan con los filtros aplicados</p>
                    <button class="btn btn-secondary" onclick="limpiarFiltros()">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </button>
                `;
                noResultsMessage.style.display = 'block';
            } else if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }
        
        // Función global para limpiar filtros
        window.limpiarFiltros = function() {
            if (searchInput) searchInput.value = '';
            if (estadoFilter) estadoFilter.value = '';
            if (fechaFilter) fechaFilter.value = '';
            
            filtrarPedidos();
            
            showAlert({
                type: 'info',
                title: 'Filtros limpiados',
                message: 'Se muestran todos los pedidos disponibles',
                duration: 3000
            });
        };
        
        // Event listeners para filtros
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(filtroTimeout);
                filtroTimeout = setTimeout(filtrarPedidos, 300);
            });
        }
        
        if (estadoFilter) {
            estadoFilter.addEventListener('change', filtrarPedidos);
        }
        
        if (fechaFilter) {
            fechaFilter.addEventListener('change', filtrarPedidos);
        }
        
        console.log('Sistema de vista de pedidos inicializado correctamente');
    };
    
    initPedidosVista();
});