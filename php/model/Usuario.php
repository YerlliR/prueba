<?php

class Usuario {
    private $id;
    private $nombre;
    private $apellidos;
    private $correo;
    private $contrasenya;

    public function __construct($nombre, $apellidos, $correo, $contrasenya, $id = null) {
        $this->id = $id;
        $this->nombre = $nombre; 
        $this->apellidos = $apellidos;
        $this->correo = $correo;
        $this->contrasenya = $contrasenya;
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellidos() {
        return $this->apellidos;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function getContrasenya() {
        return $this->contrasenya;
    }

    // Setters
    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
    }

    public function setContrasenya($contrasenya) {
        $this->contrasenya = $contrasenya;
    }
}

?>
