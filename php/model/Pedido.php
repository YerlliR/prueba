<?php
class Pedido {
    private $id;
    private $idEmpresaCliente;
    private $idEmpresaProveedor;
    private $numeroPedido;
    private $fechaPedido;
    private $fechaEntregaEstimada;
    private $estado;
    private $subtotal;
    private $totalIva;
    private $total;
    private $notas;
    private $direccionEntrega;
    private $lineas; // Array de líneas de pedido
    
    // Propiedades públicas adicionales para mostrar información
    public $nombreCliente;
    public $nombreProveedor;
    public $tipo; // 'recibido' o 'enviado'

    public function __construct(
        $id = null,
        $idEmpresaCliente = null,
        $idEmpresaProveedor = null,
        $numeroPedido = null,
        $fechaPedido = null,
        $fechaEntregaEstimada = null,
        $estado = 'pendiente',
        $subtotal = 0,
        $totalIva = 0,
        $total = 0,
        $notas = null,
        $direccionEntrega = null,
        $lineas = []
    ) {
        $this->id = $id;
        $this->idEmpresaCliente = $idEmpresaCliente;
        $this->idEmpresaProveedor = $idEmpresaProveedor;
        $this->numeroPedido = $numeroPedido;
        $this->fechaPedido = $fechaPedido ?: date('Y-m-d H:i:s');
        $this->fechaEntregaEstimada = $fechaEntregaEstimada;
        $this->estado = $estado;
        $this->subtotal = $subtotal;
        $this->totalIva = $totalIva;
        $this->total = $total;
        $this->notas = $notas;
        $this->direccionEntrega = $direccionEntrega;
        $this->lineas = $lineas;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getIdEmpresaCliente() { return $this->idEmpresaCliente; }
    public function getIdEmpresaProveedor() { return $this->idEmpresaProveedor; }
    public function getNumeroPedido() { return $this->numeroPedido; }
    public function getFechaPedido() { return $this->fechaPedido; }
    public function getFechaEntregaEstimada() { return $this->fechaEntregaEstimada; }
    public function getEstado() { return $this->estado; }
    public function getSubtotal() { return $this->subtotal; }
    public function getTotalIva() { return $this->totalIva; }
    public function getTotal() { return $this->total; }
    public function getNotas() { return $this->notas; }
    public function getDireccionEntrega() { return $this->direccionEntrega; }
    public function getLineas() { return $this->lineas; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setIdEmpresaCliente($idEmpresaCliente) { $this->idEmpresaCliente = $idEmpresaCliente; }
    public function setIdEmpresaProveedor($idEmpresaProveedor) { $this->idEmpresaProveedor = $idEmpresaProveedor; }
    public function setNumeroPedido($numeroPedido) { $this->numeroPedido = $numeroPedido; }
    public function setFechaPedido($fechaPedido) { $this->fechaPedido = $fechaPedido; }
    public function setFechaEntregaEstimada($fechaEntregaEstimada) { $this->fechaEntregaEstimada = $fechaEntregaEstimada; }
    public function setEstado($estado) { $this->estado = $estado; }
    public function setSubtotal($subtotal) { $this->subtotal = $subtotal; }
    public function setTotalIva($totalIva) { $this->totalIva = $totalIva; }
    public function setTotal($total) { $this->total = $total; }
    public function setNotas($notas) { $this->notas = $notas; }
    public function setDireccionEntrega($direccionEntrega) { $this->direccionEntrega = $direccionEntrega; }
    public function setLineas($lineas) { $this->lineas = $lineas; }

    // Método para agregar una línea de pedido
    public function agregarLinea($linea) {
        $this->lineas[] = $linea;
    }

    // Método para calcular totales
    public function calcularTotales() {
        $this->subtotal = 0;
        $this->totalIva = 0;
        $this->total = 0;

        foreach ($this->lineas as $linea) {
            $this->subtotal += $linea->getSubtotal();
            $this->totalIva += ($linea->getSubtotal() * $linea->getIva() / 100);
        }

        $this->total = $this->subtotal + $this->totalIva;
    }
    
    // Métodos para obtener nombres de empresas
    public function getNombreCliente() {
        return $this->nombreCliente ?? 'Cliente';
    }
    
    public function getNombreProveedor() {
        return $this->nombreProveedor ?? 'Proveedor';
    }
}

class PedidoLinea {
    private $id;
    private $idPedido;
    private $idProducto;
    private $cantidad;
    private $precioUnitario;
    private $iva;
    private $subtotal;
    private $total;
    private $nombreProducto; // Para mostrar en las vistas

    public function __construct(
        $id = null,
        $idPedido = null,
        $idProducto = null,
        $cantidad = 1,
        $precioUnitario = 0,
        $iva = 0,
        $nombreProducto = null
    ) {
        $this->id = $id;
        $this->idPedido = $idPedido;
        $this->idProducto = $idProducto;
        $this->cantidad = $cantidad;
        $this->precioUnitario = $precioUnitario;
        $this->iva = $iva;
        $this->nombreProducto = $nombreProducto;
        $this->calcularTotales();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getIdPedido() { return $this->idPedido; }
    public function getIdProducto() { return $this->idProducto; }
    public function getCantidad() { return $this->cantidad; }
    public function getPrecioUnitario() { return $this->precioUnitario; }
    public function getIva() { return $this->iva; }
    public function getSubtotal() { return $this->subtotal; }
    public function getTotal() { return $this->total; }
    public function getNombreProducto() { return $this->nombreProducto; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setIdPedido($idPedido) { $this->idPedido = $idPedido; }
    public function setIdProducto($idProducto) { $this->idProducto = $idProducto; }
    public function setCantidad($cantidad) { 
        $this->cantidad = $cantidad; 
        $this->calcularTotales();
    }
    public function setPrecioUnitario($precioUnitario) { 
        $this->precioUnitario = $precioUnitario; 
        $this->calcularTotales();
    }
    public function setIva($iva) { 
        $this->iva = $iva; 
        $this->calcularTotales();
    }
    public function setNombreProducto($nombreProducto) { $this->nombreProducto = $nombreProducto; }

    // Calcular totales de la línea
    private function calcularTotales() {
        $this->subtotal = $this->cantidad * $this->precioUnitario;
        $this->total = $this->subtotal + ($this->subtotal * $this->iva / 100);
    }
}
?>