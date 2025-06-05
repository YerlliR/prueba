document.addEventListener('DOMContentLoaded', function() {
    const initPedidos = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initPedidos, 100);
            return;
        }
        
        // Variable global para el ID del proveedor actual
        let currentProveedorId = null;
        let currentProveedorNombre = '';
        
        // Función para abrir el modal de pedido
        window.abrirModalPedido = function(proveedorId, proveedorNombre) {
            currentProveedorId = proveedorId;
            currentProveedorNombre = proveedorNombre || 'Proveedor';
            
            showAlert({
                type: 'info',
                title: 'Preparando pedido',
                message: `Iniciando proceso de pedido con "${currentProveedorNombre}"`,
                duration: 3000
            });
            
            // Crear o mostrar el modal
            let modal = document.getElementById('modal-pedido');
            if (!modal) {
                crearModalPedido();
                modal = document.getElementById('modal-pedido');
            }
            
            // Actualizar título del modal
            document.getElementById('pedido-proveedor-nombre').textContent = currentProveedorNombre;
            
            // Cargar productos del proveedor
            cargarProductosProveedor(proveedorId);
            
            // Mostrar modal
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        };
        
        // Función para crear el modal de pedido
        function crearModalPedido() {
            const modalHtml = `
                <div id="modal-pedido" class="modal">
                    <div class="modal-content modal-pedido-content">
                        <div class="modal-header">
                            <h2>Realizar Pedido a <span id="pedido-proveedor-nombre"></span></h2>
                            <button class="modal-close"><i class="fas fa-times"></i></button>
                        </div>
                        <div class="modal-body">
                            <div class="pedido-info">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="fecha-entrega">Fecha de entrega deseada</label>
                                        <input type="date" id="fecha-entrega" class="form-control" 
                                               min="${new Date().toISOString().split('T')[0]}">
                                    </div>
                                    <div class="form-group">
                                        <label for="direccion-entrega">Dirección de entrega</label>
                                        <input type="text" id="direccion-entrega" class="form-control" 
                                               placeholder="Dirección completa de entrega">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="notas-pedido">Notas del pedido (opcional)</label>
                                    <textarea id="notas-pedido" class="form-control textarea-control" 
                                              placeholder="Instrucciones especiales, observaciones..."></textarea>
                                </div>
                            </div>
                            
                            <div class="productos-section">
                                <h3>Seleccionar Productos</h3>
                                <div class="productos-filtro">
                                    <input type="text" id="buscar-producto" class="form-control" 
                                           placeholder="Buscar producto...">
                                </div>
                                <div id="productos-container" class="productos-pedido-container">
                                    <div class="loading-spinner">Cargando productos...</div>
                                </div>
                            </div>
                            
                            <div class="pedido-resumen">
                                <h3>Resumen del Pedido</h3>
                                <div class="resumen-items" id="resumen-items">
                                    <p class="empty-resumen">No hay productos seleccionados</p>
                                </div>
                                <div class="resumen-totales">
                                    <div class="total-row">
                                        <span>Subtotal:</span>
                                        <span id="pedido-subtotal">0.00 €</span>
                                    </div>
                                    <div class="total-row">
                                        <span>IVA:</span>
                                        <span id="pedido-iva">0.00 €</span>
                                    </div>
                                    <div class="total-row total-final">
                                        <span>Total:</span>
                                        <span id="pedido-total">0.00 €</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary modal-close">Cancelar</button>
                            <button class="btn btn-primary" id="btn-confirmar-pedido">
                                <i class="fas fa-shopping-cart"></i> Confirmar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Agregar event listeners
            configurarEventListeners();
        }
        
        // Función para configurar los event listeners del modal
        function configurarEventListeners() {
            const modal = document.getElementById('modal-pedido');
            
            // Cerrar modal
            modal.querySelectorAll('.modal-close').forEach(btn => {
                btn.addEventListener('click', () => {
                    cerrarModalPedido();
                    showAlert({
                        type: 'info',
                        title: 'Pedido cancelado',
                        message: 'El proceso de pedido ha sido cancelado',
                        duration: 3000
                    });
                });
            });
            
            // Click fuera del modal
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    cerrarModalPedido();
                }
            });
            
            // Buscar productos
            const buscarInput = document.getElementById('buscar-producto');
            if (buscarInput) {
                let searchTimeout;
                buscarInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    searchTimeout = setTimeout(() => {
                        filtrarProductos(query);
                        if (query.length >= 2) {
                            showAlert({
                                type: 'info',
                                title: 'Búsqueda aplicada',
                                message: `Buscando productos que contengan "${query}"`,
                                duration: 2000
                            });
                        }
                    }, 300);
                });
            }
            
            // Confirmar pedido
            const btnConfirmar = document.getElementById('btn-confirmar-pedido');
            if (btnConfirmar) {
                btnConfirmar.addEventListener('click', confirmarPedido);
            }
        }
        
        // Función para cerrar el modal
        function cerrarModalPedido() {
            const modal = document.getElementById('modal-pedido');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
                
                // Limpiar datos
                setTimeout(() => {
                    document.getElementById('fecha-entrega').value = '';
                    document.getElementById('direccion-entrega').value = '';
                    document.getElementById('notas-pedido').value = '';
                    document.getElementById('buscar-producto').value = '';
                    document.getElementById('productos-container').innerHTML = '<div class="loading-spinner">Cargando productos...</div>';
                    actualizarResumen();
                }, 300);
            }
        }
        
        // Función para cargar productos del proveedor
        function cargarProductosProveedor(proveedorId) {
            const loadingId = showAlert({
                type: 'loading',
                title: 'Cargando catálogo',
                message: `Obteniendo productos disponibles de ${currentProveedorNombre}`,
                persistent: true
            });
            
            fetch(`../../php/actions/obtenerProductosProveedor.php?id=${proveedorId}`)
                .then(response => response.json())
                .then(data => {
                    hideAlert(loadingId);
                    
                    if (data.success) {
                        const totalProductos = Object.values(data.productos).reduce((total, categoria) => 
                            total + categoria.productos.length, 0);
                            
                        showAlert({
                            type: 'success',
                            title: 'Catálogo cargado',
                            message: `Se encontraron ${totalProductos} producto(s) disponible(s)`,
                            duration: 3000
                        });
                        
                        mostrarProductos(data.productos);
                    } else {
                        mostrarError('No se pudieron cargar los productos');
                        showAlert({
                            type: 'warning',
                            title: 'Sin productos',
                            message: 'Este proveedor no tiene productos disponibles en este momento'
                        });
                    }
                })
                .catch(error => {
                    hideAlert(loadingId);
                    console.error('Error:', error);
                    mostrarError('Error al cargar los productos');
                    showAlert({
                        type: 'error',
                        title: 'Error de conexión',
                        message: 'No se pudo cargar el catálogo. Verifica tu conexión.'
                    });
                });
        }
        
        // Función para mostrar productos
        function mostrarProductos(productosPorCategoria) {
            const container = document.getElementById('productos-container');
            
            if (Object.keys(productosPorCategoria).length === 0) {
                container.innerHTML = '<p class="empty-state">Este proveedor no tiene productos disponibles</p>';
                return;
            }
            
            let html = '';
            
            for (const [categoria, datosCategoria] of Object.entries(productosPorCategoria)) {
                html += `
                    <div class="categoria-section">
                        <h4 class="categoria-titulo" style="color: ${datosCategoria.color}">
                            ${categoria}
                        </h4>
                        <div class="productos-grid">
                `;
                
                datosCategoria.productos.forEach(producto => {
                    html += `
                        <div class="producto-pedido-card" data-producto-id="${producto.id}">
                            <div class="producto-imagen">
                                ${producto.ruta_imagen ? 
                                    `<img src="../../${producto.ruta_imagen}" alt="${producto.nombre_producto}">` :
                                    '<div class="no-imagen"><i class="fas fa-box"></i></div>'
                                }
                            </div>
                            <div class="producto-info">
                                <h5>${producto.nombre_producto}</h5>
                                <p class="producto-codigo">Código: ${producto.codigo_seguimiento}</p>
                                <div class="producto-precio">
                                    <span class="precio">${parseFloat(producto.precio).toFixed(2)} €</span>
                                    <span class="iva-info">IVA: ${producto.iva}%</span>
                                </div>
                            </div>
                            <div class="producto-cantidad">
                                <button class="btn-cantidad btn-menos" data-producto='${JSON.stringify(producto)}'>
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="cantidad-input" value="0" min="0" 
                                       data-producto-id="${producto.id}"
                                       data-precio="${producto.precio}"
                                       data-iva="${producto.iva}"
                                       data-nombre="${producto.nombre_producto}">
                                <button class="btn-cantidad btn-mas" data-producto='${JSON.stringify(producto)}'>
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = html;
            
            // Agregar event listeners a los botones de cantidad
            container.querySelectorAll('.btn-menos').forEach(btn => {
                btn.addEventListener('click', function() {
                    const producto = JSON.parse(this.dataset.producto);
                    cambiarCantidad(producto.id, -1);
                });
            });
            
            container.querySelectorAll('.btn-mas').forEach(btn => {
                btn.addEventListener('click', function() {
                    const producto = JSON.parse(this.dataset.producto);
                    cambiarCantidad(producto.id, 1);
                });
            });
            
            container.querySelectorAll('.cantidad-input').forEach(input => {
                input.addEventListener('change', function() {
                    const cantidad = Math.max(0, parseInt(this.value) || 0);
                    this.value = cantidad;
                    
                    if (cantidad > 0) {
                        const nombreProducto = this.dataset.nombre;
                        showAlert({
                            type: 'success',
                            title: 'Producto añadido',
                            message: `${cantidad} unidad(es) de "${nombreProducto}" añadida(s) al pedido`,
                            duration: 2000
                        });
                    }
                    
                    actualizarResumen();
                });
            });
        }
        
        // Función para cambiar cantidad
        function cambiarCantidad(productoId, cambio) {
            const input = document.querySelector(`.cantidad-input[data-producto-id="${productoId}"]`);
            if (input) {
                const cantidadActual = parseInt(input.value || 0);
                const nuevaCantidad = Math.max(0, cantidadActual + cambio);
                input.value = nuevaCantidad;
                
                if (cambio > 0 && nuevaCantidad > cantidadActual) {
                    showAlert({
                        type: 'info',
                        title: 'Cantidad aumentada',
                        message: `Producto añadido al pedido (${nuevaCantidad} unidades)`,
                        duration: 1500
                    });
                } else if (cambio < 0 && nuevaCantidad < cantidadActual) {
                    if (nuevaCantidad === 0) {
                        showAlert({
                            type: 'warning',
                            title: 'Producto eliminado',
                            message: 'Producto eliminado del pedido',
                            duration: 2000
                        });
                    } else {
                        showAlert({
                            type: 'info',
                            title: 'Cantidad reducida',
                            message: `Cantidad actualizada (${nuevaCantidad} unidades)`,
                            duration: 1500
                        });
                    }
                }
                
                actualizarResumen();
            }
        }
        
        // Función para actualizar el resumen del pedido
        function actualizarResumen() {
            const productos = [];
            let subtotal = 0;
            let totalIva = 0;
            
            // Recopilar productos con cantidad > 0
            document.querySelectorAll('.cantidad-input').forEach(input => {
                const cantidad = parseInt(input.value || 0);
                if (cantidad > 0) {
                    const precio = parseFloat(input.dataset.precio);
                    const iva = parseFloat(input.dataset.iva);
                    const subtotalProducto = cantidad * precio;
                    const ivaProducto = subtotalProducto * (iva / 100);
                    
                    productos.push({
                        id: input.dataset.productoId,
                        nombre: input.dataset.nombre,
                        cantidad: cantidad,
                        precio: precio,
                        iva: iva,
                        subtotal: subtotalProducto,
                        totalIva: ivaProducto
                    });
                    
                    subtotal += subtotalProducto;
                    totalIva += ivaProducto;
                }
            });
            
            // Mostrar resumen
            const resumenContainer = document.getElementById('resumen-items');
            if (productos.length === 0) {
                resumenContainer.innerHTML = '<p class="empty-resumen">No hay productos seleccionados</p>';
            } else {
                let html = '<div class="resumen-lista">';
                productos.forEach(prod => {
                    html += `
                        <div class="resumen-item">
                            <div class="item-info">
                                <span class="item-nombre">${prod.nombre}</span>
                                <span class="item-detalle">${prod.cantidad} x ${prod.precio.toFixed(2)} €</span>
                            </div>
                            <span class="item-total">${prod.subtotal.toFixed(2)} €</span>
                        </div>
                    `;
                });
                html += '</div>';
                resumenContainer.innerHTML = html;
            }
            
            // Actualizar totales
            const total = subtotal + totalIva;
            document.getElementById('pedido-subtotal').textContent = subtotal.toFixed(2) + ' €';
            document.getElementById('pedido-iva').textContent = totalIva.toFixed(2) + ' €';
            document.getElementById('pedido-total').textContent = total.toFixed(2) + ' €';
            
            // Habilitar/deshabilitar botón de confirmar
            const btnConfirmar = document.getElementById('btn-confirmar-pedido');
            btnConfirmar.disabled = productos.length === 0;
            
            if (productos.length > 0 && total > 0) {
                btnConfirmar.style.opacity = '1';
                btnConfirmar.style.cursor = 'pointer';
            } else {
                btnConfirmar.style.opacity = '0.6';
                btnConfirmar.style.cursor = 'not-allowed';
            }
        }
        
        // Función para filtrar productos
        function filtrarProductos(busqueda) {
            const termino = busqueda.toLowerCase().trim();
            const cards = document.querySelectorAll('.producto-pedido-card');
            let productosVisibles = 0;
            
            cards.forEach(card => {
                const nombre = card.querySelector('h5').textContent.toLowerCase();
                const codigo = card.querySelector('.producto-codigo').textContent.toLowerCase();
                
                if (!termino || nombre.includes(termino) || codigo.includes(termino)) {
                    card.style.display = '';
                    productosVisibles++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Mostrar mensaje si no hay resultados
            if (termino && productosVisibles === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Sin resultados',
                    message: `No se encontraron productos que coincidan con "${busqueda}"`,
                    duration: 3000
                });
            } else if (termino && productosVisibles > 0) {
                showAlert({
                    type: 'success',
                    title: 'Productos encontrados',
                    message: `Se encontraron ${productosVisibles} producto(s)`,
                    duration: 2000
                });
            }
        }
        
        // Función para confirmar pedido
        function confirmarPedido() {
            // Recopilar productos seleccionados
            const productos = [];
            document.querySelectorAll('.cantidad-input').forEach(input => {
                const cantidad = parseInt(input.value || 0);
                if (cantidad > 0) {
                    productos.push({
                        id: input.dataset.productoId,
                        cantidad: cantidad,
                        precio: parseFloat(input.dataset.precio),
                        iva: parseFloat(input.dataset.iva)
                    });
                }
            });
            
            if (productos.length === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Pedido vacío',
                    message: 'Debe seleccionar al menos un producto para continuar con el pedido'
                });
                return;
            }
            
            // Validar campos opcionales
            const fechaEntrega = document.getElementById('fecha-entrega').value;
            const direccionEntrega = document.getElementById('direccion-entrega').value.trim();
            const notas = document.getElementById('notas-pedido').value.trim();
            
            // Mostrar confirmación
            const totalProductos = productos.reduce((sum, p) => sum + p.cantidad, 0);
            const totalPedido = productos.reduce((sum, p) => {
                const subtotal = p.cantidad * p.precio;
                const iva = subtotal * (p.iva / 100);
                return sum + subtotal + iva;
            }, 0);
            
            mostrarConfirmacionPedido(totalProductos, totalPedido.toFixed(2), () => {
                procesarPedido(productos, fechaEntrega, direccionEntrega, notas);
            });
        }
        
        // Función para mostrar confirmación de pedido
        function mostrarConfirmacionPedido(totalProductos, totalPrecio, callback) {
            const modalHtml = `
                <div class="modal active" id="modal-confirmar-pedido" style="z-index: 10002;">
                    <div class="modal-content" style="max-width: 450px;">
                        <div class="modal-header">
                            <h2 style="color: var(--primary-color); display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-shopping-cart"></i>
                                Confirmar Pedido
                            </h2>
                        </div>
                        <div class="modal-body">
                            <p style="font-size: 16px; font-weight: 600; margin-bottom: 15px;">¿Confirmas el pedido a "${currentProveedorNombre}"?</p>
                            <div style="background: var(--bg-color); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span>Productos:</span>
                                    <span style="font-weight: 600;">${totalProductos} unidades</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Total:</span>
                                    <span style="font-weight: 600; color: var(--primary-color);">${totalPrecio} €</span>
                                </div>
                            </div>
                            <p style="color: var(--text-light); font-size: 14px; line-height: 1.5;">El pedido se enviará al proveedor y podrás hacer seguimiento desde la sección "Pedidos Enviados".</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="btn-cancelar-confirmar-pedido">Cancelar</button>
                            <button class="btn btn-primary" id="btn-confirmar-confirmar-pedido">
                                <i class="fas fa-check"></i> Confirmar Pedido
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            const modalConfirmar = document.getElementById('modal-confirmar-pedido');
            const btnCancelar = document.getElementById('btn-cancelar-confirmar-pedido');
            const btnConfirmar = document.getElementById('btn-confirmar-confirmar-pedido');
            
            const cerrarModalConfirmar = () => {
                modalConfirmar.classList.remove('active');
                setTimeout(() => modalConfirmar.remove(), 300);
            };
            
            btnCancelar.addEventListener('click', cerrarModalConfirmar);
            btnConfirmar.addEventListener('click', () => {
                cerrarModalConfirmar();
                callback();
            });
            
            modalConfirmar.addEventListener('click', (e) => {
                if (e.target === modalConfirmar) cerrarModalConfirmar();
            });
        }
        
        // Función para procesar el pedido
        function procesarPedido(productos, fechaEntrega, direccionEntrega, notas) {
            // Obtener datos del pedido
            const pedidoData = {
                idProveedor: currentProveedorId,
                fechaEntrega: fechaEntrega,
                direccionEntrega: direccionEntrega,
                notas: notas,
                productos: productos
            };
            
            // Deshabilitar botón
            const btnConfirmar = document.getElementById('btn-confirmar-pedido');
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            
            const processingId = showAlert({
                type: 'loading',
                title: 'Procesando pedido...',
                message: `Enviando pedido a ${currentProveedorNombre}`,
                persistent: true
            });
            
            // Enviar pedido
            fetch('../../php/actions/crearPedido.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(pedidoData)
            })
            .then(response => response.json())
            .then(data => {
                hideAlert(processingId);
                
                if (data.success) {
                    showAlert({
                        type: 'success',
                        title: 'Pedido enviado',
                        message: data.message || `Tu pedido ha sido enviado correctamente a ${currentProveedorNombre}`
                    });
                    
                    cerrarModalPedido();
                    
                    // Mostrar información adicional
                    setTimeout(() => {
                        showAlert({
                            type: 'info',
                            title: 'Seguimiento del pedido',
                            message: 'Puedes hacer seguimiento del pedido en la sección "Pedidos Enviados"',
                            duration: 5000
                        });
                    }, 2000);
                    
                    // Opcional: redirigir a pedidos enviados
                    setTimeout(() => {
                        if (confirm('¿Deseas ir a la sección de Pedidos Enviados para ver tu pedido?')) {
                            window.location.href = '../../php/view/pedidos-enviados.php';
                        }
                    }, 4000);
                } else {
                    showAlert({
                        type: 'error',
                        title: 'Error al enviar pedido',
                        message: data.message || 'No se pudo procesar el pedido'
                    });
                }
            })
            .catch(error => {
                hideAlert(processingId);
                console.error('Error:', error);
                showAlert({
                    type: 'error',
                    title: 'Error de conexión',
                    message: 'No se pudo conectar con el servidor. Verifica tu conexión.'
                });
            })
            .finally(() => {
                btnConfirmar.disabled = false;
                btnConfirmar.innerHTML = '<i class="fas fa-shopping-cart"></i> Confirmar Pedido';
            });
        }
        
        // Función para mostrar error en el contenedor de productos
        function mostrarError(mensaje) {
            const container = document.getElementById('productos-container');
            container.innerHTML = `
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-secondary" onclick="cargarProductosProveedor(${currentProveedorId})">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </div>
            `;
        }
        
        // Asignar event listeners a botones de pedido existentes
        document.addEventListener('click', function(e) {
            // Para botones en la vista de proveedores
            if (e.target.closest('.btn-order')) {
                e.preventDefault();
                const btn = e.target.closest('.btn-order');
                const proveedorId = btn.getAttribute('data-id');
                
                // Obtener nombre del proveedor
                const card = btn.closest('.proveedor-row, .proveedor-card');
                const nombre = card ? card.querySelector('.proveedor-name').textContent : 'Proveedor';
                
                abrirModalPedido(proveedorId, nombre);
            }
        });
        
        console.log('Sistema de pedidos inicializado correctamente');
    };
    
    initPedidos();
});