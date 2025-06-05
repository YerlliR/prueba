<?php

class Empresa {
    public $id;
    public $nombre;
    public $sector;
    public $numero_empleados;
    public $descripcion;
    public $telefono;
    public $email;
    public $sitio_web;
    public $estado;
    public $ruta_logo;
    public $usuario_id;
    public $pais;
    public $ciudad;


    // Constructor para inicializar los valores
    public function __construct($id, $nombre, $sector, $numero_empleados, $descripcion, $telefono, $email, $sitio_web, $estado, $ruta_logo, $usuario_id, $pais, $ciudad) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->sector = $sector;
        $this->numero_empleados = $numero_empleados;
        $this->descripcion = $descripcion;
        $this->telefono = $telefono;
        $this->email = $email;
        $this->sitio_web = $sitio_web;
        $this->estado = $estado;
        $this->ruta_logo = $ruta_logo;
        $this->usuario_id = $usuario_id;
        $this->pais = $pais;
        $this->ciudad = $ciudad;
    }

// Getter and Setter for 'nombre'
public function getNombre() {
    return $this->nombre;
}

public function setNombre($nombre) {
    $this->nombre = $nombre;
}

// Getter and Setter for 'sector'
public function getSector() {
    return $this->sector;
}

public function setSector($sector) {
    $this->sector = $sector;
}

// Getter and Setter for 'numero_empleados'
public function getNumeroEmpleados() {
    return $this->numero_empleados;
}

public function setNumeroEmpleados($numero_empleados) {
    $this->numero_empleados = $numero_empleados;
}

// Getter and Setter for 'descripcion'
public function getDescripcion() {
    return $this->descripcion;
}

public function setDescripcion($descripcion) {
    $this->descripcion = $descripcion;
}

// Getter and Setter for 'telefono'
public function getTelefono() {
    return $this->telefono;
}

public function setTelefono($telefono) {
    $this->telefono = $telefono;
}

// Getter and Setter for 'email'
public function getEmail() {
    return $this->email;
}

public function setEmail($email) {
    $this->email = $email;
}

// Getter and Setter for 'sitio_web'
public function getSitioWeb() {
    return $this->sitio_web;
}

public function setSitioWeb($sitio_web) {
    $this->sitio_web = $sitio_web;
}

// Getter and Setter for 'estado'
public function getEstado() {
    return $this->estado;
}

public function setEstado($estado) {
    $this->estado = $estado;
}

// Getter and Setter for 'ruta_logo'
public function getRutaLogo() {
    return $this->ruta_logo;
}

public function setRutaLogo($ruta_logo) {
    $this->ruta_logo = $ruta_logo;
}

// Getter and Setter for 'usuario_id'
public function getUsuarioId() {
    return $this->usuario_id;
}

public function setUsuarioId($usuario_id) {
    $this->usuario_id = $usuario_id;
}

// Getter and Setter for 'pais'
public function getPais() {
    return $this->pais;
}

public function setPais($pais) {
    $this->pais = $pais;
}

// Getter and Setter for 'ciudad'
public function getCiudad() {
    return $this->ciudad;
}

public function setCiudad($ciudad) {
    $this->ciudad = $ciudad;
}

public function getId() {
    return $this->id;
}


}

?>

