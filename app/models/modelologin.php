<?php

class modelologin {

    private $db;

    public function __construct() {
        $this->db = new Base;
    }

    public function comprobarLogin($mail, $pass) {
//          Ahora trabajamos con un array, en producción lo haremos con una tabla de la base de datos
        $usuarios = ["testing@prueba.com", "testing", "Test"];

        if (in_array($mail, $usuarios) && in_array($pass, $usuarios)) {
            return $usuarios[2];
        } else {
            return 0;
        }
    }

}
