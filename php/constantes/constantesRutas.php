<?php
// Definimos la ruta base al directorio principal del proyecto
define('BASE_PATH', realpath(dirname(dirname(__FILE__))));

// Definimos las rutas específicas
define('RUTA_LOGOS', BASE_PATH . "/../uploads/logosEmpresas/");
define('RUTA_DB', BASE_PATH . "/db/conexionDb.php");
define('RUTA_EMPRESA_MODEL', BASE_PATH . "/model/Empresa.php");
define('RUTA_CATEGORIA_MODEL', BASE_PATH . "/model/Categoria.php");
define('RUTA_EMPRESA_DAO', BASE_PATH . "/dao/EmpresaDao.php");
define('RUTA_USUARIO_MODEL', BASE_PATH . "/model/Usuario.php");
define('RUTA_USUARIO_DAO', BASE_PATH . "/dao/UsuarioDao.php");
?>