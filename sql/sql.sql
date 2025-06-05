
CREATE TABLE `categorias` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) NOT NULL,
  `descripcion_categoria` varchar(255) DEFAULT NULL,
  `color_categoria` varchar(50) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `fecha_creacion` date DEFAULT curdate(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `empresas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `numero_empleados` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `telefono` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `sitio_web` varchar(100) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `pais` varchar(100) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `ruta_logo` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pedidos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa_cliente` int(11) NOT NULL,
  `id_empresa_proveedor` int(11) NOT NULL,
  `numero_pedido` varchar(50) NOT NULL,
  `fecha_pedido` datetime NOT NULL DEFAULT current_timestamp(),
  `fecha_entrega_estimada` date DEFAULT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_iva` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `direccion_entrega` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `numero_pedido` (`numero_pedido`),
  KEY `idx_empresa_cliente` (`id_empresa_cliente`),
  KEY `idx_empresa_proveedor` (`id_empresa_proveedor`),
  KEY `idx_estado` (`estado`),
  KEY `idx_fecha_pedido` (`fecha_pedido`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `pedidos_lineas` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_pedido` bigint(20) unsigned NOT NULL,
  `id_producto` bigint(20) unsigned NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `iva` decimal(5,2) NOT NULL DEFAULT 0.00,
  `subtotal` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pedido` (`id_pedido`),
  KEY `idx_producto` (`id_producto`),
  CONSTRAINT `pedidos_lineas_ibfk_1` FOREIGN KEY (`id_pedido`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `productos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `codigo_seguimiento` bigint(20) NOT NULL,
  `nombre_producto` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_categoria` int(11) NOT NULL,
  `ruta_imagen` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) NOT NULL,
  `iva` decimal(5,2) DEFAULT NULL CHECK (`iva` >= 0 and `iva` <= 100),
  `id_empresa` int(11) NOT NULL,
  `fecha_creacion` date DEFAULT curdate(),
  `activo` tinyint(1) DEFAULT 1,
  `eliminado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `relaciones_empresa` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa_cliente` int(11) NOT NULL,
  `id_empresa_proveedor` int(11) NOT NULL,
  `fecha_inicio` datetime NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(50) NOT NULL DEFAULT 'activa',
  `solicitud_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_empresa_cliente` (`id_empresa_cliente`,`id_empresa_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `solicitudes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `asunto` varchar(255) NOT NULL,
  `mensaje` text NOT NULL,
  `estado` varchar(50) NOT NULL DEFAULT 'pendiente',
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `id_empresa_solicitante` int(11) DEFAULT NULL,
  `id_empresa_proveedor` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `usuarios` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasenya` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
