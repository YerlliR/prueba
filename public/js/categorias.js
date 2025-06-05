document.addEventListener('DOMContentLoaded', function() {
    const initCategorias = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initCategorias, 100);
            return;
        }
        
        const btnGestionCategorias = document.querySelector('.btn-categorias');
        const modal = document.getElementById('modal-categoria');
        const vistaCategorias = document.getElementById('vista-categorias');
        const vistaFormulario = document.getElementById('vista-formulario');
        const btnNuevaCategoria = document.getElementById('btn-nueva-categoria');
        const btnVolver = document.getElementById('btn-volver');
        const btnsCerrar = modal.querySelectorAll('.modal-close');
        const btnCancelar = modal.querySelector('.modal-cancel');
        const colorOptions = modal.querySelectorAll('.color-option');
        const categoriaCards = modal.querySelectorAll('.categoria-card');
        const categoriaForm = document.querySelector('#vista-formulario form');
        const colorInput = document.getElementById('categoria-color');
        
        // Abrir modal
        if (btnGestionCategorias) {
            btnGestionCategorias.addEventListener('click', function() {
                vistaCategorias.style.display = 'block';
                vistaFormulario.style.display = 'none';
                modal.classList.add('active');
                document.body.style.overflow = 'hidden';
            });
        }
        
        // Cambiar a vista de formulario
        if (btnNuevaCategoria) {
            btnNuevaCategoria.addEventListener('click', function() {
                vistaCategorias.style.display = 'none';
                vistaFormulario.style.display = 'block';
                
                if (!document.querySelector('.color-option.selected, .color-option.active')) {
                    colorOptions[0].classList.add('selected');
                    if (colorInput) {
                        colorInput.value = colorOptions[0].getAttribute('data-color');
                    }
                }
            });
        }
        
        // Volver a la vista de categorías
        if (btnVolver) {
            btnVolver.addEventListener('click', function(e) {
                e.preventDefault();
                vistaFormulario.style.display = 'none';
                vistaCategorias.style.display = 'block';
                limpiarFormulario();
            });
        }
        
        // Cerrar modal
        btnsCerrar.forEach(btn => {
            btn.addEventListener('click', cerrarModal);
        });
        
        if (btnCancelar) {
            btnCancelar.addEventListener('click', cerrarModal);
        }
        
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModal();
            }
        });
        
        function cerrarModal() {
            modal.classList.remove('active');
            document.body.style.overflow = 'auto';
            setTimeout(() => {
                vistaFormulario.style.display = 'none';
                vistaCategorias.style.display = 'block';
                limpiarFormulario();
            }, 300);
        }
        
        function limpiarFormulario() {
            document.getElementById('categoria-nombre').value = '';
            document.getElementById('categoria-descripcion').value = '';
            
            colorOptions.forEach((option, index) => {
                if (index === 0) {
                    option.classList.add('selected', 'active');
                    if (colorInput) {
                        colorInput.value = option.getAttribute('data-color');
                    }
                } else {
                    option.classList.remove('selected', 'active');
                }
            });
        }
        
        // Selección de color
        colorOptions.forEach(option => {
            option.addEventListener('click', function() {
                colorOptions.forEach(op => {
                    op.classList.remove('selected', 'active');
                });
                
                this.classList.add('selected', 'active');
                
                if (colorInput) {
                    colorInput.value = this.getAttribute('data-color');
                }
                
                showAlert({
                    type: 'info',
                    title: 'Color seleccionado',
                    message: 'Color actualizado para la nueva categoría',
                    duration: 2000
                });
            });
        });
        
        // Establecer color por defecto
        if (colorOptions.length > 0) {
            const selectedColor = document.querySelector('.color-option.selected, .color-option.active');
            if (!selectedColor) {
                colorOptions[0].classList.add('selected', 'active');
                if (colorInput) {
                    colorInput.value = colorOptions[0].getAttribute('data-color');
                }
            } else if (colorInput && !colorInput.value) {
                colorInput.value = selectedColor.getAttribute('data-color');
            }
        }
        
        // Eliminar categoría
        categoriaCards.forEach(card => {
            card.addEventListener('click', async function() {
                const categoriaId = this.getAttribute('data-id');
                const categoriaNombre = this.querySelector('.categoria-name').innerText;
                
                // Mostrar confirmación personalizada
                const confirmado = await mostrarConfirmacionEliminar(
                    `¿Estás seguro de eliminar la categoría "${categoriaNombre}"?`,
                    'Esta acción eliminará la categoría, pero no afectará a los productos que ya la usen.'
                );
                
                if (confirmado) {
                    // Mostrar animación de carga
                    this.style.opacity = '0.5';
                    this.style.pointerEvents = 'none';
                    
                    const loadingId = showAlert({
                        type: 'loading',
                        title: 'Eliminando categoría...',
                        message: `Eliminando "${categoriaNombre}"`,
                        persistent: true
                    });

                    try {
                        const response = await fetch('../../php/actions/eliminarCategoria.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `idCategoria=${encodeURIComponent(categoriaId)}`,
                        });
                        
                        const data = await response.json();
                        
                        hideAlert(loadingId);
                        
                        if (data.success) {
                            showAlert({
                                type: 'success',
                                title: 'Categoría eliminada',
                                message: data.message || `"${categoriaNombre}" se ha eliminado correctamente`
                            });
                            
                            this.style.transform = 'scale(0.8)';
                            this.style.opacity = '0';
                            
                            setTimeout(() => {
                                this.remove();
                                const categoriasRestantes = document.querySelectorAll('.categoria-card').length;
                                if (categoriasRestantes === 0) {
                                    const categoriaGrid = document.querySelector('.categorias-grid');
                                    categoriaGrid.innerHTML = '<p class="empty-state">No hay categorías creadas aún</p>';
                                }
                            }, 300);
                        } else {
                            this.style.opacity = '1';
                            this.style.pointerEvents = 'auto';
                            showAlert({
                                type: 'error',
                                title: 'Error al eliminar',
                                message: data.message || 'No se pudo eliminar la categoría'
                            });
                        }
                        
                    } catch (error) {
                        hideAlert(loadingId);
                        console.error('Error:', error);
                        this.style.opacity = '1';
                        this.style.pointerEvents = 'auto';
                        showAlert({
                            type: 'error',
                            title: 'Error de conexión',
                            message: 'No se pudo conectar con el servidor. Inténtalo de nuevo.'
                        });
                    }
                }
            });
        });
        
        // Función para mostrar confirmación de eliminar
        function mostrarConfirmacionEliminar(titulo, mensaje) {
            return new Promise((resolve) => {
                const modalHtml = `
                    <div class="modal active" id="modal-confirmar-eliminar-categoria" style="z-index: 10002;">
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
                                <button class="btn btn-secondary" id="btn-cancelar-eliminar-categoria">Cancelar</button>
                                <button class="btn" id="btn-confirmar-eliminar-categoria" style="background: #dc2626; color: white;">
                                    <i class="fas fa-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                document.body.style.overflow = 'hidden';
                
                const modalEliminar = document.getElementById('modal-confirmar-eliminar-categoria');
                const btnCancelar = document.getElementById('btn-cancelar-eliminar-categoria');
                const btnConfirmar = document.getElementById('btn-confirmar-eliminar-categoria');
                
                const cerrarModalEliminar = (resultado) => {
                    modalEliminar.classList.remove('active');
                    setTimeout(() => modalEliminar.remove(), 300);
                    resolve(resultado);
                };
                
                btnCancelar.addEventListener('click', () => cerrarModalEliminar(false));
                btnConfirmar.addEventListener('click', () => cerrarModalEliminar(true));
                
                modalEliminar.addEventListener('click', (e) => {
                    if (e.target === modalEliminar) cerrarModalEliminar(false);
                });
            });
        }
        
        // Envío del formulario
        if (categoriaForm) {
            categoriaForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const nombre = document.getElementById('categoria-nombre').value.trim();
                const descripcion = document.getElementById('categoria-descripcion').value.trim();
                const colorSeleccionado = document.querySelector('.color-option.selected, .color-option.active');
                
                // Validaciones con alertas
                if (!nombre) {
                    showAlert({
                        type: 'warning',
                        title: 'Campo requerido',
                        message: 'Por favor, introduce un nombre para la categoría'
                    });
                    document.getElementById('categoria-nombre').focus();
                    return false;
                }
                
                if (nombre.length < 2) {
                    showAlert({
                        type: 'warning',
                        title: 'Nombre muy corto',
                        message: 'El nombre de la categoría debe tener al menos 2 caracteres'
                    });
                    document.getElementById('categoria-nombre').focus();
                    return false;
                }
                
                if (nombre.length > 50) {
                    showAlert({
                        type: 'warning',
                        title: 'Nombre muy largo',
                        message: 'El nombre de la categoría no puede exceder 50 caracteres'
                    });
                    document.getElementById('categoria-nombre').focus();
                    return false;
                }
                
                if (!colorSeleccionado && !colorInput.value) {
                    showAlert({
                        type: 'warning',
                        title: 'Color requerido',
                        message: 'Por favor, selecciona un color para la categoría'
                    });
                    return false;
                }
                
                // Mostrar alerta de guardado
                const savingId = showAlert({
                    type: 'loading',
                    title: 'Guardando categoría...',
                    message: `Creando la categoría "${nombre}"`,
                    persistent: true
                });
                
                const formData = new FormData();
                formData.append('nombre', nombre);
                formData.append('descripcion', descripcion);
                formData.append('color', colorInput.value);
                
                fetch('../../php/actions/crearCategoria.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    hideAlert(savingId);
                    
                    if (response.ok) {
                        showAlert({
                            type: 'success',
                            title: 'Categoría creada',
                            message: `"${nombre}" se ha creado correctamente y está disponible para tus productos`
                        });
                        
                        cerrarModal();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        throw new Error('Error en la respuesta del servidor');
                    }
                })
                .catch(error => {
                    hideAlert(savingId);
                    console.error('Error:', error);
                    showAlert({
                        type: 'error',
                        title: 'Error al crear categoría',
                        message: 'No se pudo crear la categoría. Inténtalo de nuevo.'
                    });
                });
                
                return false;
            });
        }
        
        console.log('Sistema de categorías inicializado correctamente');
    };
    
    initCategorias();
});