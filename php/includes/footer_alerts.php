
<?php
// Incluir el helper de alertas si no está incluido
if (!class_exists('AlertHelper')) {
    require_once __DIR__ . '/alert_helper.php';
}
?>

<!-- Scripts del sistema de alertas -->
<script src="../../public/js/alertSystem.js"></script>

<?php
// Renderizar alertas de la sesión
echo AlertHelper::renderAlertsScript();
?>

<script>
// Función global para manejar respuestas AJAX
window.handleAjaxResponse = function(response, successCallback = null, errorCallback = null) {
    try {
        const data = typeof response === 'string' ? JSON.parse(response) : response;
        
        if (data.success) {
            // Mostrar alerta de éxito
            if (typeof showAlert === 'function') {
                showAlert({
                    type: data.alert_type || 'success',
                    title: data.title || 'Éxito',
                    message: data.message
                });
            }
            
            // Ejecutar callback de éxito si se proporciona
            if (successCallback) {
                successCallback(data);
            }
        } else {
            // Mostrar alerta de error
            if (typeof showAlert === 'function') {
                showAlert({
                    type: data.alert_type || 'error',
                    title: data.title || 'Error',
                    message: data.message
                });
            }
            
            // Ejecutar callback de error si se proporciona
            if (errorCallback) {
                errorCallback(data);
            }
        }
    } catch (error) {
        console.error('Error parsing response:', error);
        if (typeof showAlert === 'function') {
            showAlert({
                type: 'error',
                title: 'Error',
                message: 'Error al procesar la respuesta del servidor'
            });
        }
    }
};

// Configuración del sistema de alertas
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que el sistema de alertas esté disponible
    if (typeof showAlert !== 'function') {
        console.error('Sistema de alertas no disponible');
        return;
    }
    
    // Configurar duraciones personalizadas por tipo
    if (window.alertSystem) {
        window.alertSystem.defaultDuration = 4000;
    }
    
    // Manejar errores globales de JavaScript
    window.addEventListener('error', function(e) {
        console.error('JavaScript Error:', e.error);
        showAlert({
            type: 'error',
            title: 'Error de JavaScript',
            message: 'Se ha producido un error inesperado en la página',
            duration: 6000
        });
    });
    
    // Verificar conexión inicial
    if (!navigator.onLine) {
        showAlert({
            type: 'warning',
            title: 'Sin conexión a internet',
            message: 'Algunas funciones pueden no estar disponibles',
            persistent: true
        });
    }
    
    // Escuchar cambios en la conexión
    window.addEventListener('online', () => {
        showAlert({
            type: 'success',
            title: 'Conexión restaurada',
            message: 'La conexión a internet se ha restablecido correctamente'
        });
    });
    
    window.addEventListener('offline', () => {
        showAlert({
            type: 'warning',
            title: 'Conexión perdida',
            message: 'Se ha perdido la conexión a internet',
            persistent: true
        });
    });
});
</script>
