<?php

class Inicio extends Controlador {

   

    public function __construct() {
        
    
    }

    public function index() {
        $datos = [
           // 'nombre_clase' => get_class(),
           // 'submenu' => $this->submenu
        ];
        $this->vista('inicio/inicio', $datos);
    }

   
}
