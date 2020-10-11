<?php

class Inicio extends Controlador {

   

    public function __construct() {
        
    
    }

    public function index($msg=0) {
        $datos = [
           // 'nombre_clase' => get_class(),
           // 'submenu' => $this->submenu,
           'msg' => $msg
        ];
        $this->vista('inicio/inicio', $datos);
    }

   
}
