document.addEventListener('DOMContentLoaded', () => {
    const initSeleccionEmpresa = () => {
        if (typeof showAlert !== 'function') {
            setTimeout(initSeleccionEmpresa, 100);
            return;
        }
        
        const empresaSeleccionada = document.querySelectorAll('[data-empresa-id]');
        const addCompanyCard = document.querySelector('.add-company-card');

        empresaSeleccionada.forEach(card => {
            card.addEventListener('click', () => {
                const idEmpresa = card.dataset.empresaId;
                const nombreEmpresa = card.querySelector('.company-name')?.textContent || 'la empresa';
                
                // Mostrar alerta de selección
                showAlert({
                    type: 'info',
                    title: 'Seleccionando empresa',
                    message: `Configurando acceso para ${nombreEmpresa}...`,
                    duration: 3000
                });
                
                // Añadir efecto visual
                card.style.transform = 'scale(0.95)';
                card.style.transition = 'transform 0.2s ease';
                
                const loadingId = showAlert({
                    type: 'loading',
                    title: 'Iniciando sesión empresarial',
                    message: 'Configurando tu entorno de trabajo...',
                    persistent: true
                });

                fetch("../../php/functions/guardarEnSesionDesdeJs.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `idEmpresa=${encodeURIComponent(idEmpresa)}`
                })
                .then(response => response.json())
                .then(data => {
                    hideAlert(loadingId);
                    
                    if (data.success) {
                        showAlert({
                            type: 'success',
                            title: 'Empresa seleccionada',
                            message: data.message || `Acceso configurado para ${nombreEmpresa}. Redirigiendo...`,
                            duration: 3000
                        });
                        
                        // Redirigir después de un breve delay
                        setTimeout(() => {
                            window.location.href = './panelPrincipal.php';
                        }, 1500);
                    } else {
                        // Restaurar el estado visual
                        card.style.transform = '';
                        
                        showAlert({
                            type: 'error',
                            title: data.title || 'Error de acceso',
                            message: data.message || 'No se pudo acceder a la empresa seleccionada'
                        });
                        
                        if (data.alert_type === 'warning') {
                            setTimeout(() => {
                                showAlert({
                                    type: 'info',
                                    title: 'Sesión expirada',
                                    message: 'Redirigiendo al login...',
                                    duration: 3000
                                });
                                
                                setTimeout(() => {
                                    window.location.href = './login.php';
                                }, 2000);
                            }, 2000);
                        }
                    }
                })
                .catch(error => {
                    hideAlert(loadingId);
                    console.error('Error en la petición:', error);
                    
                    // Restaurar el estado visual
                    card.style.transform = '';
                    
                    showAlert({
                        type: 'error',
                        title: 'Error de conexión',
                        message: 'No se pudo conectar con el servidor. Verifica tu conexión a internet.'
                    });
                });
            });
            
            // Efectos hover mejorados
            card.addEventListener('mouseenter', () => {
                if (!card.style.transform.includes('scale(0.95)')) {
                    card.style.transform = 'translateY(-5px)';
                    card.style.transition = 'transform 0.3s ease';
                }
            });
            
            card.addEventListener('mouseleave', () => {
                if (!card.style.transform.includes('scale(0.95)')) {
                    card.style.transform = '';
                }
            });
        });

        // Gestión de añadir nueva empresa
        if (addCompanyCard) {
            addCompanyCard.addEventListener('click', () => {
                showAlert({
                    type: 'info',
                    title: 'Crear nueva empresa',
                    message: 'Redirigiendo al formulario de creación de empresa...',
                    duration: 3000
                });
                
                // Efecto visual
                addCompanyCard.style.transform = 'scale(0.95)';
                addCompanyCard.style.transition = 'transform 0.2s ease';
                
                setTimeout(() => {
                    window.location.href = './creacionEmpresa.php';
                }, 1000);
            });
            
            // Efectos hover para tarjeta de añadir
            addCompanyCard.addEventListener('mouseenter', () => {
                addCompanyCard.style.transform = 'translateY(-5px)';
                addCompanyCard.style.transition = 'transform 0.3s ease';
            });
            
            addCompanyCard.addEventListener('mouseleave', () => {
                addCompanyCard.style.transform = '';
            });
        }
        
        // Verificar si hay empresas disponibles
        const totalEmpresas = empresaSeleccionada.length;
        
        if (totalEmpresas === 0) {
            setTimeout(() => {
                showAlert({
                    type: 'warning',
                    title: 'No hay empresas',
                    message: 'Aún no has creado ninguna empresa. Crea tu primera empresa para comenzar.',
                    duration: 6000
                });
            }, 1000);
        }
        
        // Gestión de errores de navegación
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        
        if (error) {
            setTimeout(() => {
                switch (error) {
                    case 'session_expired':
                        showAlert({
                            type: 'warning',
                            title: 'Sesión expirada',
                            message: 'Tu sesión anterior ha expirado. Por favor, selecciona una empresa para continuar.',
                            duration: 6000
                        });
                        break;
                    case 'no_access':
                        showAlert({
                            type: 'error',
                            title: 'Acceso denegado',
                            message: 'No tienes permisos para acceder a la empresa solicitada.',
                            duration: 5000
                        });
                        break;
                    case 'company_not_found':
                        showAlert({
                            type: 'error',
                            title: 'Empresa no encontrada',
                            message: 'La empresa solicitada no existe o ha sido eliminada.',
                            duration: 5000
                        });
                        break;
                    default:
                        showAlert({
                            type: 'warning',
                            title: 'Aviso',
                            message: 'Hubo un problema con tu solicitud anterior. Por favor, selecciona una empresa.',
                            duration: 4000
                        });
                }
                
                // Limpiar URL después de mostrar el error
                window.history.replaceState({}, document.title, window.location.pathname);
            }, 1000);
        }
        
        // Gestión de teclas para accesibilidad
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                const focusedElement = document.activeElement;
                if (focusedElement && focusedElement.hasAttribute('data-empresa-id')) {
                    e.preventDefault();
                    focusedElement.click();
                } else if (focusedElement && focusedElement.classList.contains('add-company-card')) {
                    e.preventDefault();
                    focusedElement.click();
                }
            }
        });
        
        // Animación de entrada para las tarjetas
        const allCards = document.querySelectorAll('.company-card');
        allCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 200 + (index * 100));
        });
        
        // Información contextual sobre las empresas
        empresaSeleccionada.forEach(card => {
            const empresaStatus = card.querySelector('.company-status');
            if (empresaStatus) {
                const statusText = empresaStatus.textContent.toLowerCase();
                
                if (statusText.includes('inactiva')) {
                    // Mostrar advertencia para empresas inactivas
                    card.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        showAlert({
                            type: 'warning',
                            title: 'Empresa inactiva',
                            message: 'Esta empresa está marcada como inactiva. Contacta con el administrador si necesitas acceso.',
                            duration: 5000
                        });
                    });
                    
                    // Indicador visual
                    card.style.opacity = '0.7';
                    card.style.cursor = 'not-allowed';
                }
            }
        });
        
        console.log('Sistema de selección de empresa inicializado correctamente');
    };
    
    initSeleccionEmpresa();
});