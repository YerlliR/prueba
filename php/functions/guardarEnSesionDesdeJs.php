<?php
include_once '../dao/EmpresaDao.php';
include_once '../includes/alert_helper.php'; // ← NUEVA LÍNEA
session_start();

header('Content-Type: application/json');

$response = ['success' => false, 'message' => '', 'title' => ''];

if (isset($_POST['idEmpresa'])) {
    $idEmpresa = (int)$_POST['idEmpresa'];
    
    try {
        $empresa = findById($idEmpresa);
        
        if ($empresa) {
            // Verificar que la empresa pertenece al usuario actual
            if (!isset($_SESSION['usuario']['id'])) {
                $response = [
                    'success' => false,
                    'message' => 'Tu sesión ha expirado. Por favor, inicia sesión nuevamente.',
                    'title' => 'Sesión Expirada',
                    'alert_type' => 'warning'
                ];
            } else {
                $_SESSION['empresa'] = $empresa;
                $response = [
                    'success' => true,
                    'message' => "Has seleccionado la empresa '{$empresa['nombre']}'. Redirigiendo al panel principal...",
                    'title' => 'Empresa Seleccionada',
                    'alert_type' => 'success'
                ];
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'La empresa seleccionada no existe o no tienes acceso a ella.',
                'title' => 'Empresa No Encontrada',
                'alert_type' => 'error'
            ];
        }
    } catch (Exception $e) {
        error_log("Error en guardarEnSesionDesdeJs.php: " . $e->getMessage());
        $response = [
            'success' => false,
            'message' => 'Error del sistema. Inténtalo de nuevo.',
            'title' => 'Error del Sistema',
            'alert_type' => 'error'
        ];
    }
} else {
    $response = [
        'success' => false,
        'message' => 'ID de empresa no proporcionado.',
        'title' => 'Datos Faltantes',
        'alert_type' => 'warning'
    ];
}

echo json_encode($response);
?>