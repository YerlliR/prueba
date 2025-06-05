<?php

class Categoria {
    public $id;
    public $nombre;
    public $descripcion;
    public $color;
    public $empresaId;
    public $fechaCreacion;

    public function __construct($id, $nombre, $descripcion, $color, $empresaId, $fechaCreacion) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->color = $color;
        $this->empresaId = $empresaId;
        $this->fechaCreacion = $fechaCreacion;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getColor() {
        return $this->color;
    }

    public function setColor($color) {
        $this->color = $color;
    }

    public function getEmpresaId() {
        return $this->empresaId;
    }

    public function setEmpresaId($empresaId) {
        $this->empresaId = $empresaId;
    }

    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion($fechaCreacion) {
        $this->fechaCreacion = $fechaCreacion;
    }

}
?>