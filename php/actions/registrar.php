<?php
include_once '../constantes/constantesRutas.php';  
include_once RUTA_DB;
include_once RUTA_USUARIO_MODEL;
include_once RUTA_USUARIO_DAO;
include_once '../includes/alert_helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Comprobamos si se han enviado los datos del formulario
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificamos que todos los campos necesarios existen
    if(isset($_POST['signup-name']) && isset($_POST['signup-email']) && isset($_POST['signup-password']) && isset($_POST['signup-confirm-password'])) {
        $name = trim($_POST['signup-name']);
        $email = trim($_POST['signup-email']);
        $password = $_POST['signup-password'];
        $confirmPassword = $_POST['signup-confirm-password'];

        // Validaciones mejoradas
        if (strlen($name) < 2) {
            AlertHelper::error('El nombre debe tener al menos 2 caracteres', 'Nombre Inválido');
            header('Location: ../view/login.php');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            AlertHelper::error('El formato del correo electrónico no es válido', 'Email Inválido');
            header('Location: ../view/login.php');
            exit;
        }

        if (strlen($password) < 6) {
            AlertHelper::error('La contraseña debe tener al menos 6 caracteres', 'Contraseña Débil');
            header('Location: ../view/login.php');
            exit;
        }

        if($password !== $confirmPassword) {
            AlertHelper::error('Las contraseñas no coinciden. Verifícalas e inténtalo de nuevo.', 'Contraseñas Diferentes');
            header('Location: ../view/login.php');
            exit;
        }

        try {
            // Verificar si el correo ya existe en la base de datos
            $correoExistente = correoExistente($email);

            if ($correoExistente) {
                AlertHelper::warning('Este correo electrónico ya está registrado. Intenta iniciar sesión o usa otro email.', 'Email Duplicado');
                header('Location: ../view/login.php');
                exit;
            }

            // Separación de nombre y apellidos
            $nombreDividido = explode(" ", $name);
            $nombre = array_shift($nombreDividido);
            $apellidos = implode(" ", $nombreDividido);

            // Encriptar contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Registrar usuario en la base de datos
            $resultado = registrarUsuario($nombre, $apellidos, $email, $hashedPassword);
            
            if ($resultado) {
                AlertHelper::success('¡Cuenta creada exitosamente! Ya puedes iniciar sesión con tu email y contraseña.', 'Registro Completado');
                header("Location: ../view/login.php");
                exit;
            } else {
                AlertHelper::error('No se pudo crear la cuenta. Inténtalo de nuevo.', 'Error de Registro');
                header('Location: ../view/login.php');
                exit;
            }
            
        } catch (Exception $e) {
            AlertHelper::error('Error del sistema: ' . $e->getMessage(), 'Error Crítico');
            header('Location: ../view/login.php');
            exit;
        }
        
    } else {
        AlertHelper::error('Todos los campos son obligatorios para crear tu cuenta', 'Formulario Incompleto');
        header('Location: ../view/login.php');
        exit;
    }
} else {
    AlertHelper::error('Método de acceso no permitido', 'Acceso Denegado');
    header('Location: ../view/login.php');
    exit;
}
?>