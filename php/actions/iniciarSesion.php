<?php
session_start();

require_once '../constantes/constantesRutas.php';
require_once RUTA_DB;
require_once RUTA_USUARIO_MODEL;
require_once RUTA_USUARIO_DAO;
require_once '../includes/alert_helper.php'; // ← NUEVA LÍNEA

// Verificar que se reciban los datos del formulario
if (!isset($_POST['login-email'], $_POST['login-password'])) {
    AlertHelper::error('Por favor, completa todos los campos para iniciar sesión', 'Datos Faltantes');
    header('Location: ../view/login.php');
    exit;
}

$email = trim($_POST['login-email']);
$password = $_POST['login-password'];

// Validaciones básicas
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    AlertHelper::error('El formato del email no es válido', 'Email Inválido');
    header('Location: ../view/login.php');
    exit;
}

if (empty($password)) {
    AlertHelper::error('La contraseña es obligatoria', 'Contraseña Faltante');
    header('Location: ../view/login.php');
    exit;
}

try {
    // Buscar usuario por correo
    $user = findUserByEmail($email);

    if ($user && password_verify($password, $user['contrasenya'])) {
        // Crear objeto Usuario
        $usuario = new Usuario($user['nombre'], $user['apellidos'], $user['correo'], null, $user['id']);
        
        // Guardar en sesión
        $_SESSION['usuario'] = [
            'id' => $user['id'],
            'nombre' => $user['nombre'],
            'apellidos' => $user['apellidos'],
            'correo' => $user['correo']
        ];

        AlertHelper::success("¡Bienvenido de vuelta, {$user['nombre']}! Tu sesión se ha iniciado correctamente.", 'Inicio de Sesión Exitoso');
        
        // Redirigir al usuario
        header("Location: ../view/seleccionEmpresa.php");
        exit;

    } else {
        AlertHelper::error('Email o contraseña incorrectos. Verifica tus datos e inténtalo de nuevo.', 'Credenciales Inválidas');
        header('Location: ../view/login.php');
        exit;
    }

} catch (PDOException $e) {
    // Log del error real (no mostrar al usuario)
    error_log("Error de base de datos en login: " . $e->getMessage());
    AlertHelper::error('Error temporal del sistema. Inténtalo de nuevo en unos minutos.', 'Error del Sistema');
    header('Location: ../view/login.php');
    exit;
} catch (Exception $e) {
    error_log("Error general en login: " . $e->getMessage());
    AlertHelper::error('Se produjo un error inesperado. Inténtalo de nuevo.', 'Error Inesperado');
    header('Location: ../view/login.php');
    exit;
}
?>