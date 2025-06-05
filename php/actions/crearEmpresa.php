<?php  
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once '../constantes/constantesRutas.php';  
include_once RUTA_DB;
include_once RUTA_EMPRESA_MODEL;
include_once RUTA_EMPRESA_DAO;
include_once '../includes/alert_helper.php'; // ← NUEVA LÍNEA

if (isset($_POST['nombre']) && isset($_POST['sector']) && isset($_POST['numero_empleados']) && isset($_POST['descripcion']) && isset($_POST['telefono']) && isset($_POST['email']) && isset($_POST['sitio_web']) && isset($_POST['estado']) && isset($_POST['pais']) && isset($_POST['ciudad'])) {
    
    $nombre = trim($_POST['nombre']);
    $sector = $_POST['sector'];
    $numero_empleados = (int)$_POST['numero_empleados'];
    $descripcion = trim($_POST['descripcion']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $sitio_web = trim($_POST['sitio_web']);
    $estado = isset($_POST['estado']) ? 'Activa' : 'Inactiva';
    $pais = $_POST['pais'];
    $ciudad = trim($_POST['ciudad']);

    // Validaciones
    if (strlen($nombre) < 2) {
        AlertHelper::error('El nombre de la empresa debe tener al menos 2 caracteres', 'Nombre Inválido');
        header('Location: ../view/creacionEmpresa.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        AlertHelper::error('El formato del email no es válido', 'Email Inválido');
        header('Location: ../view/creacionEmpresa.php');
        exit;
    }

    if (isset($_SESSION['usuario']) && isset($_SESSION['usuario']['id'])) {
        $usuario_id = $_SESSION['usuario']['id'];
    } else {
        AlertHelper::error('No hay sesión de usuario activa. Por favor, inicie sesión nuevamente.', 'Sesión Expirada');
        header('Location: ../view/login.php');
        exit;
    }
    
    $ruta_logo = '';
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['logo']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            AlertHelper::error('El logo debe ser una imagen (JPG, PNG, GIF)', 'Formato de Archivo Inválido');
            header('Location: ../view/creacionEmpresa.php');
            exit;
        }
        
        if ($_FILES['logo']['size'] > 2 * 1024 * 1024) { // 2MB
            AlertHelper::error('El logo no puede ser mayor a 2MB', 'Archivo Muy Grande');
            header('Location: ../view/creacionEmpresa.php');
            exit;
        }
        
        $nombre_logo = uniqid() . "_" . basename($_FILES['logo']['name']);
        $ruta_destino = RUTA_LOGOS . $nombre_logo;
        
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $ruta_destino)) {
            $ruta_logo = '/uploads/logosEmpresas/' . $nombre_logo;
        } else {
            AlertHelper::warning('No se pudo subir el logo, pero la empresa se creará sin logo', 'Logo No Subido');
        }
    }

    try {
        $empresa = new Empresa(null, $nombre, $sector, $numero_empleados, $descripcion, $telefono, $email, $sitio_web, $estado, $ruta_logo, $usuario_id, $pais, $ciudad);
        $result = crearEmpresa($empresa);
        
        if ($result > 0) {
            AlertHelper::success("La empresa '{$nombre}' se ha creado exitosamente. Ya puedes seleccionarla y comenzar a gestionar tus productos y pedidos.", 'Empresa Creada');
            header("Location: ../view/seleccionEmpresa.php");
            exit;
        } else {
            AlertHelper::error('No se pudo crear la empresa. Inténtalo de nuevo más tarde.', 'Error al Crear');
            header('Location: ../view/creacionEmpresa.php');
            exit;
        }
    } catch (Exception $e) {
        AlertHelper::error('Error del sistema: ' . $e->getMessage(), 'Error Crítico');
        header('Location: ../view/creacionEmpresa.php');
        exit;
    }
    
} else {
    AlertHelper::error('Faltan datos obligatorios para crear la empresa', 'Formulario Incompleto');
    header('Location: ../view/creacionEmpresa.php');
    exit;
}
?>