<?php
// Configuracion de acceso a la base de datos


define('DB_HOST','localhost');
define('DB_USUARIO','root');
define('DB_PASSWORD','');
define('DB_NOMBRE','agricola');

// Ruta de la aplicacion
define('RUTA_APP', dirname(dirname(__FILE__)));

define('RUTA_URL','http://localhost/mvc_limpio_original');
// NOMBRE DEL SITIO
define('NOMBRE_SITIO', 'MVC ORIGINAL');

//Ruta para subida de ficheros:
define("DOCUMENTOS_PRIVADOS", RUTA_APP."/documentos/");
