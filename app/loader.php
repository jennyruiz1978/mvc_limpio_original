<?php
//Cargamos la libreria
require_once('config/configurar.php');

require_once('helpers/url_helpers.php');


//Autoload php
spl_autoload_register(function($nombreClase){
    require_once('librerias/' . $nombreClase . '.php');
});