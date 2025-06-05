document.addEventListener('DOMContentLoaded', function() {
    const initProductos = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initProductos, 100);
            return;
        }
        
        // Nuevo producto
        const nuevoProductoCard = document.querySelector('.add-producto-card');
        if (nuevoProductoCard) {
            nuevoProductoCard.addEventListener('click', () => {
                showAlert({
                    type: 'info',
                    title: 'Creando producto',
                    message: 'Redirigiendo al formulario de creación...',
                    duration: 2000
                });
                
                setTimeout(() => {
                    window.location.href = './creacionProducto.php';
                }, 1000);
            });
        }
        
        // Eliminar producto
        const eliminarProducto = document.querySelectorAll('.btn-delete');
        eliminarProducto.forEach(eliminar => {
            eliminar.addEventListener('click', async () => {
                const idProducto = eliminar.dataset.productoId;
                const productoCard = eliminar.closest('.producto-card');
                const nombreProducto = productoCard?.querySelector('.producto-title')?.textContent || 'este producto';
                
                // Mostrar confirmación personalizada
                const confirmado = await mostrarConfirmacion(
                    `¿Estás seguro de que deseas eliminar "${nombreProducto}"?`,
                    'Esta acción no se puede deshacer. El producto será eliminado permanentemente de tu catálogo.'
                );
                
                if (confirmado) {
                    // Mostrar alerta de carga
                    const loadingId = showAlert({
                        type: 'loading',
                        title: 'Eliminando producto...',
                        message: `Eliminando "${nombreProducto}" del catálogo`,
                        persistent: true
                    });
                    
                    try {
                        const response = await fetch('../../php/actions/eliminarProducto.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `idProducto=${encodeURIComponent(idProducto)}`,
                        });
                        
                        const data = await response.json();
                        
                        hideAlert(loadingId);
                        
                        if (data.success) {
                            showAlert({
                                type: 'success',
                                title: 'Producto eliminado',
                                message: data.message || `"${nombreProducto}" se ha eliminado correctamente`,
                                duration: 4000
                            });
                            
                            productoCard.style.transition = 'all 0.5s ease';
                            productoCard.style.transform = 'scale(0.8)';
                            productoCard.style.opacity = '0';
                            
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            showAlert({
                                type: 'error',
                                title: 'Error al eliminar',
                                message: data.message || 'No se pudo eliminar el producto'
                            });
                        }
                        
                    } catch (error) {
                        hideAlert(loadingId);
                        console.error('Error:', error);
                        showAlert({
                            type: 'error',
                            title: 'Error de conexión',
                            message: 'No se pudo conectar con el servidor. Verifica tu conexión e inténtalo de nuevo.'
                        });
                    }
                }
            });
        });
        
        // Editar producto
        const editarProducto = document.querySelectorAll('.btn-edit');
        editarProducto.forEach(editar => {
            editar.addEventListener('click', () => {
                const idProducto = editar.dataset.productoId;
                const productoCard = editar.closest('.producto-card');
                const nombreProducto = productoCard?.querySelector('.producto-title')?.textContent || 'el producto';
                
                showAlert({
                    type: 'info',
                    title: 'Cargando editor',
                    message: `Cargando formulario de edición para "${nombreProducto}"...`,
                    duration: 2000
                });
                
                setTimeout(() => {
                    window.location.href = `../../php/view/edicionProducto.php?id=${idProducto}`;
                }, 1000);
            });
        });
        
        // Búsqueda de productos
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();
                
                searchTimeout = setTimeout(() => {
                    if (query.length >= 2) {
                        filtrarProductos(query);
                    } else if (query.length === 0) {
                        mostrarTodosLosProductos();
                    }
                }, 300);
            });
        }
        
        // Función para mostrar confirmación personalizada
        function mostrarConfirmacion(titulo, mensaje) {
            return new Promise((resolve) => {
                const modalHtml = `
                    <div class="modal active" id="modal-confirmar-eliminacion" style="z-index: 10001;">
                        <div class="modal-content" style="max-width: 450px;">
                            <div class="modal-header">
                                <h2 style="color: #dc2626; display: flex; align-items: center; gap: 10px;">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Confirmar eliminación
                                </h2>
                            </div>
                            <div class="modal-body">
                                <p style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">${titulo}</p>
                                <p style="color: var(--text-light); line-height: 1.5;">${mensaje}</p>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" id="btn-cancelar-eliminacion">Cancelar</button>
                                <button class="btn" id="btn-confirmar-eliminacion" style="background: #dc2626; color: white;">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.body.style.overflow = 'hidden';
                
                const modal = document.getElementById('modal-confirmar-eliminacion');
                const btnCancelar = document.getElementById('btn-cancelar-eliminacion');
                const btnConfirmar = document.getElementById('btn-confirmar-eliminacion');
                
                const cerrarModal = (resultado) => {
                    modal.classList.remove('active');
                    document.body.style.overflow = 'auto';
                    setTimeout(() => modal.remove(), 300);
                    resolve(resultado);
                };
                
                btnCancelar.addEventListener('click', () => cerrarModal(false));
                btnConfirmar.addEventListener('click', () => cerrarModal(true));
                
                modal.addEventListener('click', (e) => {
                    if (e.target === modal) cerrarModal(false);
                });
            });
        }
        
        // Función para filtrar productos
        function filtrarProductos(query) {
            const productos = document.querySelectorAll('.producto-card:not(.add-producto-card)');
            let productosVisibles = 0;
            
            productos.forEach(producto => {
                const nombre = producto.querySelector('.producto-title')?.textContent.toLowerCase() || '';
                const categoria = producto.querySelector('.producto-category')?.textContent.toLowerCase() || '';
                const descripcion = producto.querySelector('.producto-desc')?.textContent.toLowerCase() || '';
                
                const termino = query.toLowerCase();
                const coincide = nombre.includes(termino) || 
                               categoria.includes(termino) || 
                               descripcion.includes(termino);
                
                if (coincide) {
                    producto.style.display = '';
                    productosVisibles++;
                } else {
                    producto.style.display = 'none';
                }
            });
            
            if (productosVisibles === 0) {
                mostrarMensajeNoResultados(query);
            } else {
                ocultarMensajeNoResultados();
                showAlert({
                    type: 'info',
                    title: 'Búsqueda completada',
                    message: `Se encontraron ${productosVisibles} producto(s) que coinciden con "${query}"`,
                    duration: 3000
                });
            }
        }
        
        // Función para mostrar todos los productos
        function mostrarTodosLosProductos() {
            const productos = document.querySelectorAll('.producto-card');
            productos.forEach(producto => {
                producto.style.display = '';
            });
            ocultarMensajeNoResultados();
        }
        
        // Función para mostrar mensaje de no resultados
        function mostrarMensajeNoResultados(query) {
            let noResultsMessage = document.getElementById('no-results-message');
            if (!noResultsMessage) {
                noResultsMessage = document.createElement('div');
                noResultsMessage.id = 'no-results-message';
                noResultsMessage.className = 'no-results-message';
                noResultsMessage.style.cssText = `
                    grid-column: 1 / -1;
                    text-align: center;
                    padding: 40px;
                    color: var(--text-light);
                `;
                
                const productosGrid = document.querySelector('.productos-grid');
                if (productosGrid) {
                    productosGrid.appendChild(noResultsMessage);
                }
            }
            
            noResultsMessage.innerHTML = `
                <i class="fas fa-search" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                <h3>No se encontraron productos</h3>
                <p>No hay productos que coincidan con "${query}"</p>
                <button class="btn btn-secondary" onclick="document.querySelector('.search-input').value = ''; document.querySelector('.search-input').dispatchEvent(new Event('input'));">
                    Limpiar búsqueda
                </button>
            `;
            noResultsMessage.style.display = 'block';
        }
        
        // Función para ocultar mensaje de no resultados
        function ocultarMensajeNoResultados() {
            const noResultsMessage = document.getElementById('no-results-message');
            if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }
        
        // Alerta de bienvenida si es la primera vez
        const productos = document.querySelectorAll('.producto-card:not(.add-producto-card)');
        if (productos.length === 0) {
            setTimeout(() => {
                showAlert({
                    type: 'info',
                    title: '¡Bienvenido a tu catálogo!',
                    message: 'Comienza añadiendo tu primer producto para gestionar tu inventario.',
                    duration: 5000
                });
            }, 1000);
        }
        
        console.log('Sistema de productos inicializado correctamente');
    };
    
    initProductos();
});