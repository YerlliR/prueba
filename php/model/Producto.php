<?php
class Producto {

    private $id;
    private $codigoSeguimiento;
    private $nombreProducto;
    private $descripcion;
    private $idCategoria;
    private $rutaImagen;
    private $precio;
    private $iva;
    private $idEmpresa;
    private $fechaCreacion;
    private $activo;
    private $eliminado;

    public function __construct($id, $codigoSeguimiento, $nombreProducto, $descripcion, $idCategoria, $rutaImagen, $precio, $iva, $idEmpresa, $fechaCreacion, $activo, $eliminado = false) {
        $this->id = $id;
        $this->codigoSeguimiento = $codigoSeguimiento;
        $this->nombreProducto = $nombreProducto;
        $this->descripcion = $descripcion;
        $this->idCategoria = $idCategoria;
        $this->rutaImagen = $rutaImagen;
        $this->precio = $precio;
        $this->iva = $iva;
        $this->idEmpresa = $idEmpresa;
        $this->fechaCreacion = $fechaCreacion;
        $this->activo = $activo;
        $this->eliminado = $eliminado;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCodigoSeguimiento() {
        return $this->codigoSeguimiento;
    }

    public function setCodigoSeguimiento($codigoSeguimiento) {
        $this->codigoSeguimiento = $codigoSeguimiento;
    }

    public function getNombreProducto() {
        return $this->nombreProducto;
    }

    public function setNombreProducto($nombreProducto) {
        $this->nombreProducto = $nombreProducto;
    }

    public function getDescripcion() {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion) {
        $this->descripcion = $descripcion;
    }

    public function getIdCategoria() {
        return $this->idCategoria;
    }

    public function setIdCategoria($idCategoria) {
        $this->idCategoria = $idCategoria;
    }

    public function getRutaImagen() {
        return $this->rutaImagen;
    }

    public function setRutaImagen($rutaImagen) {
        $this->rutaImagen = $rutaImagen;
    }

    public function getPrecio() {
        return $this->precio;
    }

    public function setPrecio($precio) {
        $this->precio = $precio;
    }

    public function getIva() {
        return $this->iva;
    }

    public function setIva($iva) {
        $this->iva = $iva;
    }

    public function getIdEmpresa() {
        return $this->idEmpresa;
    }

    public function setIdEmpresa($idEmpresa) {
        $this->idEmpresa = $idEmpresa;
    }

    public function getFechaCreacion() {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion($fechaCreacion) {
        $this->fechaCreacion = $fechaCreacion;
    }

    public function isActivo() {
        return $this->activo;
    }

    public function setActivo($activo) {
        $this->activo = $activo;
    }

    public function isEliminado() {
        return $this->eliminado;
    }

    public function setEliminado($eliminado) {
        $this->eliminado = $eliminado;
    }
}

?>