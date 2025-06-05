// Ejemplos de uso del sistema de alertas en JavaScript
// Archivo: public/js/alert_usage_examples.js

// ===== FUNCIÓN AUXILIAR PARA MANEJAR RESPUESTAS AJAX =====
function handleAjaxResponse(response, successCallback = null, errorCallback = null) {
    try {
        const data = typeof response === 'string' ? JSON.parse(response) : response;
        
        if (data.success) {
            // Mostrar alerta de éxito
            showAlert({
                type: data.alert_type || 'success',
                title: data.title || 'Éxito',
                message: data.message
            });
            
            // Ejecutar callback de éxito si se proporciona
            if (successCallback) {
                successCallback(data);
            }
        } else {
            // Mostrar alerta de error
            showAlert({
                type: data.alert_type || 'error',
                title: data.title || 'Error',
                message: data.message
            });
            
            // Ejecutar callback de error si se proporciona
            if (errorCallback) {
                errorCallback(data);
            }
        }
    } catch (error) {
        console.error('Error parsing response:', error);
        showAlert({
            type: 'error',
            title: 'Error',
            message: 'Error al procesar la respuesta del servidor'
        });
    }
}

// ===== EJEMPLOS DE USO EN DIFERENTES CONTEXTOS =====

// 1. Modificación para productos.js
document.addEventListener('DOMContentLoaded', () => {
    // Eliminar producto con alertas
    const eliminarProducto = document.querySelectorAll('.btn-delete');

    eliminarProducto.forEach(eliminar => {
        eliminar.addEventListener('click', () => {
            if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
                const idProducto = eliminar.dataset.productoId;
                
                // Mostrar alerta de carga
                const loadingId = showAlert({
                    type: 'loading',
                    title: 'Eliminando producto...',
                    message: 'Por favor espera mientras se elimina el producto',
                    persistent: true
                });
                
                fetch('../../php/actions/eliminarProducto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `idProducto=${encodeURIComponent(idProducto)}`,
                })
                .then(response => response.text())
                .then(data => {
                    // Ocultar alerta de carga
                    hideAlert(loadingId);
                    
                    // Manejar respuesta
                    handleAjaxResponse(data, () => {
                        // Éxito: recargar página después de un breve delay
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    });
                })
                .catch(error => {
                    hideAlert(loadingId);
                    console.error('Error al eliminar el producto:', error);
                    showAlert({
                        type: 'error',
                        title: 'Error de conexión',
                        message: 'No se pudo conectar con el servidor'
                    });
                });
            }
        });
    });
});

// 2. Modificación para exploradorProveedores.js - envío de solicitudes
function enviarSolicitudConAlertas(solicitudData) {
    // Mostrar alerta de envío
    const sendingId = showAlert({
        type: 'loading',
        title: 'Enviando solicitud...',
        message: 'Tu solicitud está siendo enviada al proveedor',
        persistent: true
    });
    
    fetch('../../php/actions/procesarSolicitud.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(solicitudData)
    })
    .then(response => response.json())
    .then(data => {
        hideAlert(sendingId);
        
        handleAjaxResponse(data, (responseData) => {
            // Cerrar modal si existe
            const modal = document.getElementById('modal-contact');
            if (modal) {
                modal.classList.remove('active');
                document.body.style.overflow = 'auto';
            }
            
            // Limpiar formulario
            document.getElementById('modal-contact-subject').value = '';
            document.getElementById('modal-contact-message').value = '';
        });
    })
    .catch(error => {
        hideAlert(sendingId);
        console.error('Error:', error);
        showAlert({
            type: 'error',
            title: 'Error de conexión',
            message: 'No se pudo enviar la solicitud. Revisa tu conexión.'
        });
    });
}

// 3. Modificación para pedidos.js - crear pedido
function confirmarPedidoConAlertas(pedidoData) {
    // Validación con alertas
    if (!pedidoData.productos || pedidoData.productos.length === 0) {
        showAlert({
            type: 'warning',
            title: 'Pedido incompleto',
            message: 'Debes seleccionar al menos un producto para continuar'
        });
        return;
    }
    
    // Mostrar progreso
    const processingId = showAlert({
        type: 'loading',
        title: 'Procesando pedido...',
        message: 'Creando tu pedido, esto puede tomar unos segundos',
        persistent: true
    });
    
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
        
        handleAjaxResponse(data, (responseData) => {
            // Cerrar modal de pedido
            cerrarModalPedido();
            
            // Mostrar opción de ir a pedidos
            showAlert({
                type: 'success',
                title: 'Pedido creado exitosamente',
                message: 'Tu pedido ha sido enviado. ¿Deseas ver tus pedidos?',
                duration: 6000,
                action: () => {
                    window.location.href = '../../php/view/pedidos-enviados.php';
                }
            });
        });
    })
    .catch(error => {
        hideAlert(processingId);
        console.error('Error:', error);
        showAlert({
            type: 'error',
            title: 'Error al crear pedido',
            message: 'No se pudo procesar tu pedido. Inténtalo de nuevo.'
        });
    });
}

// 4. Modificación para categorias.js - crear categoría
function guardarCategoriaConAlertas(formData) {
    const savingId = showAlert({
        type: 'loading',
        title: 'Guardando categoría...',
        message: 'Creando la nueva categoría',
        persistent: true
    });
    
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
                message: 'La nueva categoría se ha guardado correctamente'
            });
            
            // Recargar página
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
            title: 'Error al guardar',
            message: 'No se pudo crear la categoría. Inténtalo de nuevo.'
        });
    });
}

// 5. Sistema de validación de formularios con alertas
function validarFormularioConAlertas(form, rules) {
    const errors = [];
    
    for (const [field, rule] of Object.entries(rules)) {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input) continue;
        
        const value = input.value.trim();
        
        // Validación requerido
        if (rule.required && !value) {
            errors.push(`${rule.label || field} es obligatorio`);
        }
        
        // Validación de longitud mínima
        if (rule.minLength && value.length < rule.minLength) {
            errors.push(`${rule.label || field} debe tener al menos ${rule.minLength} caracteres`);
        }
        
        // Validación de email
        if (rule.email && value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            errors.push(`${rule.label || field} debe ser un email válido`);
        }
        
        // Validación personalizada
        if (rule.custom && !rule.custom(value)) {
            errors.push(rule.customMessage || `${rule.label || field} no es válido`);
        }
    }
    
    if (errors.length > 0) {
        showAlert({
            type: 'warning',
            title: 'Formulario incompleto',
            message: errors[0], // Mostrar solo el primer error
            duration: 5000
        });
        return false;
    }
    
    return true;
}

// 6. Función para manejar cambios de estado de pedidos
function cambiarEstadoPedidoConAlertas(pedidoId, nuevoEstado) {
    const estados = {
        'procesando': 'El pedido está siendo procesado',
        'completado': 'Marcando pedido como completado',
        'cancelado': 'Cancelando el pedido'
    };
    
    const loadingId = showAlert({
        type: 'loading',
        title: 'Actualizando estado...',
        message: estados[nuevoEstado] || 'Actualizando estado del pedido',
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
        
        handleAjaxResponse(data, () => {
            // Actualizar la UI del pedido
            const pedidoCard = document.querySelector(`[data-pedido-id="${pedidoId}"]`);
            if (pedidoCard) {
                const estadoElement = pedidoCard.querySelector('.pedido-estado');
                if (estadoElement) {
                    estadoElement.className = `pedido-estado estado-${nuevoEstado}`;
                    estadoElement.textContent = nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1);
                }
                
                // Actualizar botones disponibles
                actualizarBotonesPedido(pedidoCard, nuevoEstado);
            }
        });
    })
    .catch(error => {
        hideAlert(loadingId);
        console.error('Error:', error);
        showAlert({
            type: 'error',
            title: 'Error de conexión',
            message: 'No se pudo actualizar el estado del pedido'
        });
    });
}

// 7. Función para manejar solicitudes aceptar/rechazar
function manejarSolicitudConAlertas(solicitudId, accion) {
    const acciones = {
        'aceptar': 'Aceptando solicitud...',
        'rechazar': 'Rechazando solicitud...'
    };
    
    const loadingId = showAlert({
        type: 'loading',
        title: acciones[accion] || 'Procesando...',
        message: 'Actualizando el estado de la solicitud',
        persistent: true
    });
    
    fetch('../../php/actions/actualizarSolicitud.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${solicitudId}&accion=${accion}`
    })
    .then(response => response.json())
    .then(data => {
        hideAlert(loadingId);
        
        handleAjaxResponse(data, () => {
            // Recargar las solicitudes
            const activeTab = document.querySelector('.solicitud-tab-btn.active').dataset.tab;
            loadSolicitudes(activeTab);
            
            // Mostrar mensaje adicional si se aceptó
            if (accion === 'aceptar') {
                setTimeout(() => {
                    showAlert({
                        type: 'info',
                        title: 'Relación establecida',
                        message: 'Ahora puedes ver esta empresa en tu lista de proveedores',
                        duration: 5000
                    });
                }, 1000);
            }
        });
    })
    .catch(error => {
        hideAlert(loadingId);
        console.error('Error:', error);
        showAlert({
            type: 'error',
            title: 'Error de conexión',
            message: 'No se pudo procesar la solicitud'
        });
    });
}

// 8. Alertas para operaciones de archivos/imágenes
function subirImagenConAlertas(formData, successCallback) {
    const uploadingId = showAlert({
        type: 'loading',
        title: 'Subiendo imagen...',
        message: 'Procesando el archivo, por favor espera',
        persistent: true
    });
    
    fetch('../../php/actions/subirImagen.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideAlert(uploadingId);
        
        if (data.success) {
            showAlert({
                type: 'success',
                title: 'Imagen subida',
                message: 'La imagen se ha procesado correctamente'
            });
            
            if (successCallback) {
                successCallback(data);
            }
        } else {
            showAlert({
                type: 'error',
                title: 'Error al subir imagen',
                message: data.message || 'No se pudo procesar la imagen'
            });
        }
    })
    .catch(error => {
        hideAlert(uploadingId);
        console.error('Error:', error);
        showAlert({
            type: 'error',
            title: 'Error de conexión',
            message: 'No se pudo subir la imagen. Verifica tu conexión.'
        });
    });
}

// 9. Sistema de confirmación con alertas personalizadas
function confirmarAccionConAlerta(config) {
    return new Promise((resolve) => {
        const alertId = showAlert({
            type: config.type || 'warning',
            title: config.title || 'Confirmar acción',
            message: config.message + '\n\n¿Deseas continuar?',
            persistent: true,
            action: () => {
                hideAlert(alertId);
                resolve(true);
            }
        });
        
        // Agregar botón de cancelar
        setTimeout(() => {
            const alertElement = document.querySelector(`[data-alert-id="${alertId}"]`);
            if (alertElement) {
                const cancelBtn = document.createElement('button');
                cancelBtn.textContent = 'Cancelar';
                cancelBtn.className = 'alert-action-btn cancel';
                cancelBtn.style.cssText = `
                    margin-left: 10px;
                    padding: 5px 12px;
                    border: 1px solid #ccc;
                    background: white;
                    border-radius: 4px;
                    cursor: pointer;
                `;
                
                cancelBtn.onclick = () => {
                    hideAlert(alertId);
                    resolve(false);
                };
                
                alertElement.querySelector('.alert-content').appendChild(cancelBtn);
            }
        }, 100);
    });
}

// 10. Alertas de conexión/estado
function mostrarEstadoConexion() {
    if (!navigator.onLine) {
        showAlert({
            type: 'warning',
            title: 'Sin conexión',
            message: 'No tienes conexión a internet. Algunas funciones pueden no funcionar.',
            persistent: true
        });
    }
}

// Event listeners para estado de conexión
window.addEventListener('online', () => {
    showAlert({
        type: 'success',
        title: 'Conexión restaurada',
        message: 'Tu conexión a internet se ha restablecido'
    });
});

window.addEventListener('offline', () => {
    mostrarEstadoConexion();
});

// 11. Wrapper para fetch con manejo automático de errores
function fetchConAlertas(url, options = {}, config = {}) {
    const showLoading = config.showLoading !== false;
    const loadingMessage = config.loadingMessage || 'Cargando...';
    
    let loadingId = null;
    
    if (showLoading) {
        loadingId = showAlert({
            type: 'loading',
            title: loadingMessage,
            message: 'Por favor espera...',
            persistent: true
        });
    }
    
    return fetch(url, options)
        .then(response => {
            if (loadingId) hideAlert(loadingId);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.json();
        })
        .then(data => {
            if (config.autoHandleResponse !== false) {
                handleAjaxResponse(data);
            }
            return data;
        })
        .catch(error => {
            if (loadingId) hideAlert(loadingId);
            
            console.error('Fetch error:', error);
            
            if (config.autoHandleErrors !== false) {
                showAlert({
                    type: 'error',
                    title: 'Error de conexión',
                    message: config.errorMessage || 'No se pudo completar la operación'
                });
            }
            
            throw error;
        });
}

// ===== EJEMPLOS DE USO PRÁCTICO =====

// Uso en formulario de contacto
document.querySelector('#contact-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Validar formulario
    const isValid = validarFormularioConAlertas(this, {
        email: { required: true, email: true, label: 'Email' },
        message: { required: true, minLength: 10, label: 'Mensaje' }
    });
    
    if (!isValid) return;
    
    // Enviar con alertas
    fetchConAlertas('../../php/actions/contacto.php', {
        method: 'POST',
        body: formData
    }, {
        loadingMessage: 'Enviando mensaje...'
    }).then(() => {
        this.reset();
    });
});

// Uso en eliminación de elementos
function eliminarElementoConConfirmacion(id, tipo) {
    confirmarAccionConAlerta({
        type: 'warning',
        title: 'Confirmar eliminación',
        message: `¿Estás seguro de que deseas eliminar este ${tipo}? Esta acción no se puede deshacer.`
    }).then(confirmed => {
        if (confirmed) {
            fetchConAlertas(`../../php/actions/eliminar${tipo}.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${id}`
            }, {
                loadingMessage: `Eliminando ${tipo}...`
            }).then(() => {
                // Recargar página o actualizar UI
                setTimeout(() => window.location.reload(), 1500);
            });
        }
    });
}

// Inicialización automática del sistema
document.addEventListener('DOMContentLoaded', () => {
    // Verificar estado de conexión
    mostrarEstadoConexion();
    
    // Mostrar alertas guardadas en la sesión (si existen)
    // Este script debería ser incluido en el footer de las páginas PHP
});

// Exportar funciones para uso global
window.handleAjaxResponse = handleAjaxResponse;
window.fetchConAlertas = fetchConAlertas;
window.confirmarAccionConAlerta = confirmarAccionConAlerta;
window.validarFormularioConAlertas = validarFormularioConAlertas;