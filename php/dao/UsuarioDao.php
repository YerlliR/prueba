<?php
// Verifica si las constantes ya están definidas
if (!defined('RUTA_DB')) {
    include_once '../constantes/constantesRutas.php';
}
include_once RUTA_DB;

function correoExistente($email) {
    if($email != null) {
        try {
            $db = new conexionDb();
            $conn = $db->getConnection();
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $db->closeConnection();
            
            return ($user != null);
        } catch (PDOException $e) {
            echo "Error al verificar correo: " . $e->getMessage();
            return false;
        }
    }
    return false;
}

function registrarUsuario($nombre, $apellidos, $email, $password) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("INSERT INTO usuarios (nombre, apellidos, correo, contrasenya) VALUES (:nombre, :apellidos, :email, :password)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $result = $stmt->execute();
        $db->closeConnection();
        
        return $result;
    } catch (PDOException $e) {
        throw new Exception("Error al registrar usuario: " . $e->getMessage());
    }
}


function findUserByEmail($email) {
    try {
        $db = new conexionDb();
        $conn = $db->getConnection();
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE correo = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $db->closeConnection();
        
        return $user;
    } catch (PDOException $e) {
        throw new Exception("Error al buscar usuario por correo: " . $e->getMessage());
    }
}
?>