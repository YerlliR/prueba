<?php
class AlertHelper {
    
    /**
     * Inicializar sesión de forma segura
     */
    private static function iniciarSesion() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Añadir alerta a la sesión para mostrar en la siguiente página
     */
    public static function addAlert($type, $message, $title = '') {
        self::iniciarSesion();
        
        if (!isset($_SESSION['alerts'])) {
            $_SESSION['alerts'] = [];
        }
        
        $_SESSION['alerts'][] = [
            'type' => $type,
            'message' => $message,
            'title' => $title,
            'timestamp' => time()
        ];
    }
    
    /**
     * Métodos de conveniencia para diferentes tipos de alertas
     */
    public static function success($message, $title = 'Éxito') {
        self::addAlert('success', $message, $title);
    }
    
    public static function error($message, $title = 'Error') {
        self::addAlert('error', $message, $title);
    }
    
    public static function warning($message, $title = 'Atención') {
        self::addAlert('warning', $message, $title);
    }
    
    public static function info($message, $title = 'Información') {
        self::addAlert('info', $message, $title);
    }
    
    /**
     * Obtener alertas y limpiar la sesión
     */
    public static function getAlerts() {
        self::iniciarSesion();
        
        if (!isset($_SESSION['alerts'])) {
            return [];
        }
        
        $alerts = $_SESSION['alerts'];
        unset($_SESSION['alerts']);
        return $alerts;
    }
    
    /**
     * Generar JavaScript para mostrar alertas
     */
    public static function renderAlertsScript() {
        $alerts = self::getAlerts();
        if (empty($alerts)) {
            return '';
        }
        
        $script = '<script>';
        $script .= 'document.addEventListener("DOMContentLoaded", function() {';
        $script .= 'if (typeof showAlert === "function") {';
        
        foreach ($alerts as $alert) {
            $message = addslashes($alert['message']);
            $title = addslashes($alert['title']);
            $type = $alert['type'];
            
            $script .= "setTimeout(() => {";
            $script .= "showAlert({";
            $script .= "type: '{$type}',";
            $script .= "title: '{$title}',";
            $script .= "message: '{$message}'";
            $script .= "});";
            $script .= "}, 500);";
        }
        
        $script .= '} else {';
        $script .= 'console.warn("Sistema de alertas no disponible");';
        $script .= '}';
        $script .= '});';
        $script .= '</script>';
        
        return $script;
    }
    
    /**
     * Respuesta JSON con alerta incluida
     */
    public static function jsonResponse($success, $message, $data = [], $title = '') {
        $response = [
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'alert_type' => $success ? 'success' : 'error'
        ];
        
        if ($title) {
            $response['title'] = $title;
        }
        
        return json_encode($response);
    }
    
    /**
     * Limpiar alertas antiguas (más de 1 hora)
     */
    public static function cleanOldAlerts() {
        self::iniciarSesion();
        
        if (!isset($_SESSION['alerts'])) {
            return;
        }
        
        $currentTime = time();
        $_SESSION['alerts'] = array_filter($_SESSION['alerts'], function($alert) use ($currentTime) {
            return ($currentTime - $alert['timestamp']) < 3600; // 1 hora
        });
    }
}