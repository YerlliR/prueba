document.addEventListener('DOMContentLoaded', function() {
    const initMisProveedores = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initMisProveedores, 100);
            return;
        }
        
        // Referencias a elementos del DOM
        const searchInput = document.querySelector('.search-input');
        const sectorFilter = document.getElementById('filter-sector');
        
        // ===== FILTRADO DE PROVEEDORES =====
        
        let filtroTimeout;
        
        // Filtrado por búsqueda
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(filtroTimeout);
                filtroTimeout = setTimeout(() => {
                    filtrarProveedores();
                    
                    const query = this.value.trim();
                    if (query.length >= 2) {
                        showAlert({
                            type: 'info',
                            title: 'Búsqueda aplicada',
                            message: `Buscando proveedores que coincidan con "${query}"`,
                            duration: 2000
                        });
                    }
                }, 300);
            });
        }
        
        // Filtrado por sector
        if (sectorFilter) {
            sectorFilter.addEventListener('change', function() {
                filtrarProveedores();
                
                const sector = this.value;
                if (sector) {
                    showAlert({
                        type: 'info',
                        title: 'Filtro de sector aplicado',
                        message: `Mostrando proveedores del sector: ${sector.charAt(0).toUpperCase() + sector.slice(1)}`,
                        duration: 3000
                    });
                }
            });
        }
        
        function filtrarProveedores() {
            const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
            const selectedSector = sectorFilter ? sectorFilter.value.toLowerCase() : '';
            
            // Seleccionar todos los elementos de proveedor
            const proveedorRows = document.querySelectorAll('.proveedor-row');
            const proveedorCards = document.querySelectorAll('.proveedor-card');
            
            let proveedoresVisibles = 0;
            
            function debeMostrar(element) {
                const nombreProveedor = element.querySelector('.proveedor-name').textContent.toLowerCase();
                const sectorElement = element.querySelector('.tag');
                const sectorClase = sectorElement ? Array.from(sectorElement.classList).find(cls => cls.startsWith('tag-')) : '';
                const sectorProveedor = sectorClase ? sectorClase.replace('tag-', '') : '';
                
                const cumpleBusqueda = !searchTerm || 
                                      nombreProveedor.includes(searchTerm) || 
                                      sectorProveedor.includes(searchTerm);
                
                const cumpleSector = !selectedSector || sectorProveedor === selectedSector;
                
                return cumpleBusqueda && cumpleSector;
            }
            
            // Aplicar filtros a filas de tabla
            proveedorRows.forEach(row => {
                const mostrar = debeMostrar(row);
                row.style.display = mostrar ? '' : 'none';
                if (mostrar) proveedoresVisibles++;
            });
            
            // Aplicar filtros a tarjetas móviles
            proveedorCards.forEach(card => {
                const mostrar = debeMostrar(card);
                card.style.display = mostrar ? '' : 'none';
                if (!proveedorRows.length && mostrar) proveedoresVisibles++;
            });
            
            // Mostrar mensaje si no hay resultados
            mostrarMensajeNoResultados(proveedoresVisibles, searchTerm, selectedSector);
        }
        
        function mostrarMensajeNoResultados(cantidad, busqueda, sector) {
            let noResultsMessage = document.getElementById('no-results-proveedores');
            
            if (cantidad === 0 && (busqueda || sector)) {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.id = 'no-results-proveedores';
                    noResultsMessage.style.cssText = `
                        text-align: center;
                        padding: 40px;
                        color: var(--text-light);
                        border: 2px dashed var(--border-color);
                        border-radius: var(--border-radius-lg);
                        margin: 20px 0;
                    `;
                    
                    const container = document.querySelector('.proveedores-lista') || document.querySelector('.proveedores-cards');
                    if (container) {
                        container.appendChild(noResultsMessage);
                    }
                }
                
                let mensajeFiltros = '';
                if (busqueda && sector) {
                    mensajeFiltros = `búsqueda "${busqueda}" y sector "${sector}"`;
                } else if (busqueda) {
                    mensajeFiltros = `búsqueda "${busqueda}"`;
                } else if (sector) {
                    mensajeFiltros = `sector "${sector}"`;
                }
                
                noResultsMessage.innerHTML = `
                    <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                    <h3>No se encontraron proveedores</h3>
                    <p>No hay proveedores que coincidan con ${mensajeFiltros}</p>
                    <button class="btn btn-secondary" onclick="limpiarFiltrosProveedores()">
                        <i class="fas fa-times"></i> Limpiar filtros
                    </button>
                `;
                noResultsMessage.style.display = 'block';
            } else if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }
        
        // Función global para limpiar filtros
        window.limpiarFiltrosProveedores = function() {
            if (searchInput) searchInput.value = '';
            if (sectorFilter) sectorFilter.value = '';
            
            // Mostrar todos los proveedores
            document.querySelectorAll('.proveedor-row, .proveedor-card').forEach(element => {
                element.style.display = '';
            });
            
            // Ocultar mensaje de no resultados
            const noResultsMessage = document.getElementById('no-results-proveedores');
            if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
            
            showAlert({
                type: 'info',
                title: 'Filtros limpiados',
                message: 'Se muestran todos tus proveedores',
                duration: 3000
            });
        };
        
        // ===== GESTIÓN DE ELIMINACIÓN =====
        
        // Event delegation para botones de eliminar
        document.addEventListener('click', function(e) {
            // Verificar si el click fue en un botón de eliminar
            if (e.target.closest('.btn-remove')) {
                e.preventDefault();
                e.stopPropagation();
                
                const btn = e.target.closest('.btn-remove');
                const relacionId = btn.getAttribute('data-id');
                
                if (!relacionId) {
                    showAlert({
                        type: 'error',
                        title: 'Error',
                        message: 'ID de relación no encontrado. No se puede proceder con la eliminación.'
                    });
                    return;
                }
                
                // Obtener nombre del proveedor para confirmación
                const card = btn.closest('.proveedor-row, .proveedor-card');
                const nombreProveedor = card ? card.querySelector('.proveedor-name').textContent : 'este proveedor';
                
                // Mostrar confirmación personalizada
                mostrarConfirmacionEliminar(
                    `¿Estás seguro de que deseas eliminar la relación con "${nombreProveedor}"?`,
                    'Esta acción terminará la relación comercial y ya no aparecerá en tu lista de proveedores. Esta acción no se puede deshacer.',
                    () => eliminarRelacion(relacionId, btn, nombreProveedor)
                );
            }
            
            // Gestionar otros botones
            if (e.target.closest('.btn-order')) {
                const btn = e.target.closest('.btn-order');
                const proveedorId = btn.getAttribute('data-id');
                const card = btn.closest('.proveedor-row, .proveedor-card');
                const nombreProveedor = card ? card.querySelector('.proveedor-name').textContent : 'Proveedor';
                
                showAlert({
                    type: 'info',
                    title: 'Preparando pedido',
                    message: `Cargando catálogo de productos de "${nombreProveedor}"...`,
                    duration: 3000
                });
                
                // Aquí iría la lógica para abrir el modal de pedido
                setTimeout(() => {
                    if (typeof abrirModalPedido === 'function') {
                        abrirModalPedido(proveedorId, nombreProveedor);
                    } else {
                        showAlert({
                            type: 'warning',
                            title: 'Función no disponible',
                            message: 'El sistema de pedidos no está disponible en esta página'
                        });
                    }
                }, 1000);
            }
        });
        
        // Función para mostrar confirmación de eliminación
        function mostrarConfirmacionEliminar(titulo, mensaje, callback) {
            const modalHtml = `
                <div class="modal active" id="modal-confirmar-eliminar-proveedor" style="z-index: 10001;">
                    <div class="modal-content" style="max-width: 450px;">
                        <div class="modal-header">
                            <h2 style="color: #dc2626; display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Confirmar eliminación
                            </h2>
                        </div>
                        <div class="modal-body">
                            <p style="font-size: 16px; font-weight: 600; margin-bottom: 10px; color: var(--text-color);">${titulo}</p>
                            <p style="color: var(--text-light); line-height: 1.5;">${mensaje}</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="btn-cancelar-eliminar-proveedor">Cancelar</button>
                            <button class="btn" id="btn-confirmar-eliminar-proveedor" style="background: #dc2626; color: white;">
                                <i class="fas fa-trash"></i> Terminar Relación
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Eliminar modal anterior si existe  
            const modalAnterior = document.getElementById('modal-confirmar-eliminar-proveedor');
            if (modalAnterior) modalAnterior.remove();
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
            
            const modal = document.getElementById('modal-confirmar-eliminar-proveedor');
            const btnCancelar = document.getElementById('btn-cancelar-eliminar-proveedor');
            const btnConfirmar = document.getElementById('btn-confirmar-eliminar-proveedor');
            
            const cerrarModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
                setTimeout(() => modal.remove(), 300);
            };
            
            btnCancelar.addEventListener('click', () => {
                cerrarModal();
                showAlert({
                    type: 'info',
                    title: 'Operación cancelada',
                    message: 'La relación comercial se mantiene sin cambios',
                    duration: 3000
                });
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    cerrarModal();
                }
            });
            
            btnConfirmar.addEventListener('click', () => {
                cerrarModal();
                callback();
            });
        }
        
        // Función para eliminar relación
        function eliminarRelacion(relacionId, btnElement, nombreProveedor) {
            // Mostrar estado de carga en el botón
            const originalContent = btnElement.innerHTML;
            btnElement.disabled = true;
            btnElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            const loadingId = showAlert({
                type: 'loading',
                title: 'Terminando relación...',
                message: `Eliminando relación comercial con "${nombreProveedor}"`,
                persistent: true
            });
            
            // Preparar datos
            const formData = new FormData();
            formData.append('relacionId', relacionId);
            
            // Enviar solicitud
            fetch('../../php/actions/terminarRelacion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                hideAlert(loadingId);
                console.log('Respuesta del servidor:', data); // Debug
                
                if (data.success) {
                    showAlert({
                        type: 'success',
                        title: 'Relación terminada',
                        message: data.message || `La relación comercial con "${nombreProveedor}" ha sido terminada correctamente`,
                        duration: 5000
                    });
                    
                    // Encontrar y eliminar elementos del DOM
                    const elementos = document.querySelectorAll(`[data-id="${relacionId}"]`);
                    
                    elementos.forEach(elemento => {
                        const contenedor = elemento.closest('.proveedor-row, .proveedor-card');
                        if (contenedor) {
                            // Animación de salida
                            contenedor.style.transition = 'all 0.3s ease';
                            contenedor.style.opacity = '0';
                            contenedor.style.transform = 'translateX(-20px)';
                            
                            setTimeout(() => {
                                contenedor.remove();
                                verificarEstadoVacio();
                            }, 300);
                        }
                    });
                    
                    // Mostrar información adicional
                    setTimeout(() => {
                        showAlert({
                            type: 'info',
                            title: 'Información',
                            message: `"${nombreProveedor}" ya no aparecerá en tu lista de proveedores`,
                            duration: 4000
                        });
                    }, 2000);
                    
                } else {
                    // Restaurar botón en caso de error
                    btnElement.disabled = false;
                    btnElement.innerHTML = originalContent;
                    
                    showAlert({
                        type: 'error',
                        title: 'Error al terminar relación',
                        message: data.message || 'No se pudo terminar la relación comercial. Inténtalo de nuevo.'
                    });
                }
            })
            .catch(error => {
                hideAlert(loadingId);
                console.error('Error de red:', error);
                
                // Restaurar botón
                btnElement.disabled = false;
                btnElement.innerHTML = originalContent;
                
                showAlert({
                    type: 'error',
                    title: 'Error de conexión',
                    message: 'No se pudo conectar con el servidor. Verifica tu internet e inténtalo de nuevo.'
                });
            });
        }
        
        // Verificar si quedan proveedores después de eliminar
        function verificarEstadoVacio() {
            const filasVisibles = document.querySelectorAll('.proveedor-row:not([style*="display: none"])');
            const tarjetasVisibles = document.querySelectorAll('.proveedor-card:not([style*="display: none"])');
            
            if (filasVisibles.length === 0 && tarjetasVisibles.length === 0) {
                // Mostrar mensaje de estado vacío
                const containerTabla = document.querySelector('.proveedores-lista');
                const containerTarjetas = document.querySelector('.proveedores-cards');
                
                const mensajeVacio = `
                    <div class="empty-state" style="padding: 40px; text-align: center; width: 100%;">
                        <i class="fas fa-store" style="font-size: 48px; margin-bottom: 20px; color: #cbd5e1;"></i>
                        <p>No tienes proveedores vinculados actualmente.</p>
                        <p>Explora nuevos proveedores para establecer relaciones comerciales.</p>
                        <a href="explorarProveedores.php" class="btn btn-primary" style="margin-top: 20px; display: inline-block;">
                            Explorar Proveedores
                        </a>
                    </div>
                `;
                
                if (containerTabla) {
                    containerTabla.innerHTML = mensajeVacio;
                }
                
                if (containerTarjetas) {
                    containerTarjetas.innerHTML = mensajeVacio;
                }
                
                showAlert({
                    type: 'info',
                    title: 'Lista vacía',
                    message: 'Ya no tienes proveedores vinculados. Explora nuevos proveedores para establecer relaciones comerciales.',
                    duration: 6000
                });
            }
        }
        
        // ===== ALERTAS INFORMATIVAS AL CARGAR =====
        setTimeout(() => {
            const totalProveedores = document.querySelectorAll('.proveedor-row, .proveedor-card').length;
            
            if (totalProveedores === 0) {
                showAlert({
                    type: 'info',
                    title: 'Lista de proveedores',
                    message: 'Aún no tienes proveedores vinculados. Explora el catálogo para encontrar nuevos socios comerciales.',
                    duration: 5000
                });
            } else {
                showAlert({
                    type: 'success',
                    title: 'Proveedores cargados',
                    message: `Tienes ${totalProveedores} proveedor(es) disponible(s) en tu red comercial`,
                    duration: 4000
                });
            }
        }, 1000);
        
        console.log('Sistema de mis proveedores inicializado correctamente');
    };
    
    initMisProveedores();
});