document.addEventListener('DOMContentLoaded', () => {
    const initCreacionProducto = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initCreacionProducto, 100);
            return;
        }
        
        const imageUploadContainer = document.getElementById('imageUploadContainer');
        const inputImagen = document.getElementById('imagen');
        const previewImage = document.getElementById('previewImage');
        const productoForm = document.getElementById('productoForm');

        if (!imageUploadContainer || !inputImagen || !previewImage) {
            showAlert({
                type: 'error',
                title: 'Error de inicialización',
                message: 'No se pudieron cargar todos los elementos del formulario'
            });
            return;
        }

        // Click en el contenedor para abrir selector de archivo
        imageUploadContainer.addEventListener('click', () => {
            inputImagen.click();
            showAlert({
                type: 'info',
                title: 'Seleccionar imagen',
                message: 'Elige una imagen para tu producto (JPG, PNG, máx. 2MB)',
                duration: 3000
            });
        });

        // Vista previa al seleccionar archivo
        inputImagen.addEventListener('change', (event) => {
            const file = event.target.files[0];
            
            if (file) {
                // Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type.toLowerCase())) {
                    showAlert({
                        type: 'error',
                        title: 'Formato no válido',
                        message: 'Por favor, selecciona una imagen en formato JPG, PNG o GIF'
                    });
                    inputImagen.value = '';
                    return;
                }
                
                // Validar tamaño de archivo (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    showAlert({
                        type: 'error',
                        title: 'Archivo muy grande',
                        message: 'La imagen no puede ser mayor a 2MB. Por favor, selecciona una imagen más pequeña.'
                    });
                    inputImagen.value = '';
                    return;
                }
                
                const loadingId = showAlert({
                    type: 'loading',
                    title: 'Procesando imagen...',
                    message: 'Cargando vista previa de la imagen',
                    persistent: true
                });
                
                const reader = new FileReader();
                
                reader.onload = (e) => {
                    hideAlert(loadingId);
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                    imageUploadContainer.classList.add('has-image');
                    
                    showAlert({
                        type: 'success',
                        title: 'Imagen cargada',
                        message: `Imagen "${file.name}" cargada correctamente`,
                        duration: 3000
                    });
                };
                
                reader.onerror = () => {
                    hideAlert(loadingId);
                    showAlert({
                        type: 'error',
                        title: 'Error al cargar imagen',
                        message: 'No se pudo procesar la imagen seleccionada'
                    });
                };
                
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop mejorado
        imageUploadContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadContainer.classList.add('drag-over');
            
            // Mostrar hint solo la primera vez
            if (!imageUploadContainer.dataset.dragHintShown) {
                showAlert({
                    type: 'info',
                    title: 'Arrastra y suelta',
                    message: 'Suelta la imagen aquí para cargarla',
                    duration: 2000
                });
                imageUploadContainer.dataset.dragHintShown = 'true';
            }
        });

        imageUploadContainer.addEventListener('dragleave', (e) => {
            // Solo remover la clase si realmente salimos del contenedor
            if (!imageUploadContainer.contains(e.relatedTarget)) {
                imageUploadContainer.classList.remove('drag-over');
            }
        });

        imageUploadContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadContainer.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length === 0) {
                showAlert({
                    type: 'warning',
                    title: 'Sin archivos',
                    message: 'No se detectaron archivos válidos'
                });
                return;
            }
            
            const file = files[0];
            
            if (!file.type.startsWith('image/')) {
                showAlert({
                    type: 'error',
                    title: 'Archivo no válido',
                    message: 'Por favor, arrastra solo archivos de imagen'
                });
                return;
            }
            
            // Simular selección de archivo
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            inputImagen.files = dataTransfer.files;
            
            // Disparar evento change
            inputImagen.dispatchEvent(new Event('change', { bubbles: true }));
        });
        
        // Validación en tiempo real del formulario
        const codigoInput = document.getElementById('codigo_seguimiento');
        const nombreInput = document.getElementById('nombre_producto');
        const precioInput = document.getElementById('precio');
        const ivaInput = document.getElementById('iva');
        const categoriaSelect = document.getElementById('categoria');
        
        // Validación del código de seguimiento
        if (codigoInput) {
            codigoInput.addEventListener('blur', function() {
                const codigo = this.value.trim();
                if (codigo && codigo.length < 3) {
                    showAlert({
                        type: 'warning',
                        title: 'Código muy corto',
                        message: 'El código de seguimiento debe tener al menos 3 caracteres',
                        duration: 3000
                    });
                    this.focus();
                }
            });
        }
        
        // Validación del nombre del producto
        if (nombreInput) {
            nombreInput.addEventListener('blur', function() {
                const nombre = this.value.trim();
                if (nombre && nombre.length < 2) {
                    showAlert({
                        type: 'warning',
                        title: 'Nombre muy corto',
                        message: 'El nombre del producto debe tener al menos 2 caracteres',
                        duration: 3000
                    });
                    this.focus();
                } else if (nombre && nombre.length > 100) {
                    showAlert({
                        type: 'warning',
                        title: 'Nombre muy largo',
                        message: 'El nombre del producto no puede exceder 100 caracteres',
                        duration: 3000
                    });
                }
            });
        }
        
        // Validación del precio
        if (precioInput) {
            precioInput.addEventListener('blur', function() {
                const precio = parseFloat(this.value);
                if (this.value && (isNaN(precio) || precio <= 0)) {
                    showAlert({
                        type: 'warning',
                        title: 'Precio inválido',
                        message: 'El precio debe ser un número mayor a 0',
                        duration: 3000
                    });
                    this.focus();
                } else if (precio > 999999) {
                    showAlert({
                        type: 'warning',
                        title: 'Precio muy alto',
                        message: 'El precio no puede exceder 999,999€',
                        duration: 3000
                    });
                }
            });
        }
        
        // Validación del IVA
        if (ivaInput) {
            ivaInput.addEventListener('blur', function() {
                const iva = parseFloat(this.value);
                if (this.value && (isNaN(iva) || iva < 0 || iva > 100)) {
                    showAlert({
                        type: 'warning',
                        title: 'IVA inválido',
                        message: 'El IVA debe ser un porcentaje entre 0 y 100',
                        duration: 3000
                    });
                    this.focus();
                }
            });
        }
        
        // Ayuda contextual para categorías
        if (categoriaSelect) {
            categoriaSelect.addEventListener('focus', function() {
                if (this.options.length <= 1) {
                    showAlert({
                        type: 'info',
                        title: 'Sin categorías',
                        message: 'Puedes crear nuevas categorías desde la sección de Productos',
                        duration: 4000
                    });
                }
            });
            
            categoriaSelect.addEventListener('change', function() {
                if (this.value) {
                    const categoriaSeleccionada = this.options[this.selectedIndex].text;
                    showAlert({
                        type: 'success',
                        title: 'Categoría seleccionada',
                        message: `Producto será clasificado en: ${categoriaSeleccionada}`,
                        duration: 2000
                    });
                }
            });
        }
        
        // Manejo del envío del formulario
        if (productoForm) {
            productoForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Validaciones finales
                const errores = [];
                
                const codigo = codigoInput?.value.trim();
                const nombre = nombreInput?.value.trim();
                const precio = precioInput?.value;
                const categoria = categoriaSelect?.value;
                
                if (!codigo) {
                    errores.push('El código de seguimiento es obligatorio');
                } else if (codigo.length < 3) {
                    errores.push('El código de seguimiento debe tener al menos 3 caracteres');
                }
                
                if (!nombre) {
                    errores.push('El nombre del producto es obligatorio');
                } else if (nombre.length < 2) {
                    errores.push('El nombre debe tener al menos 2 caracteres');
                }
                
                if (!precio || parseFloat(precio) <= 0) {
                    errores.push('El precio debe ser mayor a 0');
                }
                
                if (!categoria) {
                    errores.push('Debes seleccionar una categoría');
                }
                
                if (errores.length > 0) {
                    showAlert({
                        type: 'error',
                        title: 'Formulario incompleto',
                        message: errores[0],
                        duration: 5000
                    });
                    return;
                }
                
                // Mostrar confirmación
                mostrarConfirmacionGuardado(nombre, () => {
                    enviarFormulario();
                });
            });
        }
        
        // Función para mostrar confirmación antes de guardar
        function mostrarConfirmacionGuardado(nombreProducto, callback) {
            const modalHtml = `
                <div class="modal active" id="modal-confirmar-producto" style="z-index: 10001;">
                    <div class="modal-content" style="max-width: 450px;">
                        <div class="modal-header">
                            <h2 style="color: var(--primary-color); display: flex; align-items: center; gap: 10px;">
                                <i class="fas fa-save"></i>
                                Confirmar creación
                            </h2>
                        </div>
                        <div class="modal-body">
                            <p style="font-size: 16px; font-weight: 600; margin-bottom: 10px;">¿Confirmas la creación del producto "${nombreProducto}"?</p>
                            <p style="color: var(--text-light); line-height: 1.5;">El producto se añadirá a tu catálogo y estará disponible para la venta.</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" id="btn-cancelar-producto">Cancelar</button>
                            <button class="btn btn-primary" id="btn-confirmar-producto">
                                <i class="fas fa-check"></i> Crear Producto
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            document.body.style.overflow = 'hidden';
            
            const modal = document.getElementById('modal-confirmar-producto');
            const btnCancelar = document.getElementById('btn-cancelar-producto');
            const btnConfirmar = document.getElementById('btn-confirmar-producto');
            
            const cerrarModal = () => {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
                setTimeout(() => modal.remove(), 300);
            };
            
            btnCancelar.addEventListener('click', cerrarModal);
            btnConfirmar.addEventListener('click', () => {
                cerrarModal();
                callback();
            });
            
            modal.addEventListener('click', (e) => {
                if (e.target === modal) cerrarModal();
            });
        }
        
        // Función para enviar el formulario
        function enviarFormulario() {
            const submitButton = productoForm.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            
            const savingId = showAlert({
                type: 'loading',
                title: 'Guardando producto...',
                message: 'Creando producto en el catálogo',
                persistent: true
            });
            
            // Simular el envío del formulario original
            const formData = new FormData(productoForm);
            
            fetch(productoForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                hideAlert(savingId);
                
                if (response.ok) {
                    showAlert({
                        type: 'success',
                        title: 'Producto creado',
                        message: 'El producto se ha añadido correctamente a tu catálogo',
                        duration: 4000
                    });
                    
                    setTimeout(() => {
                        showAlert({
                            type: 'info',
                            title: 'Redirigiendo...',
                            message: 'Volviendo al catálogo de productos',
                            duration: 2000
                        });
                        
                        setTimeout(() => {
                            window.location.href = './productos.php';
                        }, 1500);
                    }, 2000);
                } else {
                    throw new Error('Error en la respuesta del servidor');
                }
            })
            .catch(error => {
                hideAlert(savingId);
                console.error('Error:', error);
                
                showAlert({
                    type: 'error',
                    title: 'Error al guardar',
                    message: 'No se pudo crear el producto. Inténtalo de nuevo.'
                });
                
                // Restaurar botón
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        }
        
        // Información sobre campos opcionales
        const descripcionTextarea = document.getElementById('descripcion');
        if (descripcionTextarea) {
            descripcionTextarea.addEventListener('focus', function() {
                showAlert({
                    type: 'info',
                    title: 'Descripción del producto',
                    message: 'Una buena descripción ayuda a los clientes a entender mejor tu producto',
                    duration: 3000
                });
            });
        }
        
        // Contador de caracteres para descripción
        if (descripcionTextarea) {
            const maxLength = 500;
            let characterCountElement = document.getElementById('char-count');
            
            if (!characterCountElement) {
                characterCountElement = document.createElement('small');
                characterCountElement.id = 'char-count';
                characterCountElement.style.color = 'var(--text-light)';
                characterCountElement.style.display = 'block';
                characterCountElement.style.marginTop = '5px';
                descripcionTextarea.parentNode.appendChild(characterCountElement);
            }
            
            function updateCharCount() {
                const length = descripcionTextarea.value.length;
                characterCountElement.textContent = `${length}/${maxLength} caracteres`;
                
                if (length > maxLength * 0.8) {
                    characterCountElement.style.color = '#d97706';
                } else {
                    characterCountElement.style.color = 'var(--text-light)';
                }
                
                if (length > maxLength) {
                    showAlert({
                        type: 'warning',
                        title: 'Descripción muy larga',
                        message: 'La descripción está excediendo el límite recomendado',
                        duration: 3000
                    });
                }
            }
            
            descripcionTextarea.addEventListener('input', updateCharCount);
            updateCharCount(); // Actualizar al cargar
        }
        
        console.log('Sistema de creación de producto inicializado correctamente');
    };
    
    initCreacionProducto();
});