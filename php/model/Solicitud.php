<?php
class Solicitud {
    private $id;
    private $id_empresa_solicitante;
    private $id_empresa_proveedor;
    private $asunto;
    private $mensaje;
    private $estado; // 'pendiente', 'aceptada', 'rechazada'
    private $fecha_creacion;

    public function __construct($id, $id_empresa_solicitante, $id_empresa_proveedor, $asunto, $mensaje, $estado, $fecha_creacion = null) {
        $this->id = $id;
        $this->id_empresa_solicitante = $id_empresa_solicitante;
        $this->id_empresa_proveedor = $id_empresa_proveedor;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->estado = $estado;
        $this->fecha_creacion = $fecha_creacion ? $fecha_creacion : date('Y-m-d H:i:s');
    }

    // Getters
    public function getId() {
        return $this->id;
    }

    public function getIdEmpresaSolicitante() {
        return $this->id_empresa_solicitante;
    }

    public function getIdEmpresaProveedor() {
        return $this->id_empresa_proveedor;
    }

    public function getAsunto() {
        return $this->asunto;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getFechaCreacion() {
        return $this->fecha_creacion;
    }

    // Setters
    public function setId($id) {
        $this->id = $id;
    }

    public function setIdEmpresaSolicitante($id_empresa_solicitante) {
        $this->id_empresa_solicitante = $id_empresa_solicitante;
    }

    public function setIdEmpresaProveedor($id_empresa_proveedor) {
        $this->id_empresa_proveedor = $id_empresa_proveedor;
    }

    public function setAsunto($asunto) {
        $this->asunto = $asunto;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setFechaCreacion($fecha_creacion) {
        $this->fecha_creacion = $fecha_creacion;
    }
}
?>