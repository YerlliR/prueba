<?php

function iniciarSesionSegura() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function verificarAutenticacion($redirigir = true) {
    iniciarSesionSegura();
    
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['empresa']['id'])) {
        if ($redirigir) {
            header('Location: ../../php/view/login.php');
            exit;
        }
        return false;
    }
    return true;
}

function obtenerIdEmpresa() {
    iniciarSesionSegura();
    
    if (!isset($_SESSION['empresa']['id'])) {
        return null;
    }
    
    $idEmpresa = $_SESSION['empresa']['id'];
    if (is_array($idEmpresa)) {
        $idEmpresa = $idEmpresa[0];
    }
    
    return (int)$idEmpresa;
}