<?php

class Login extends Controlador {

    public function __construct() {
        $this->usuarioModelo = $this->modelo('modelologin');
    }

    public function index() {
       
         
       
            $this->vista('login/login');
        }
    

    public function acceder() {

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $mail = $_POST['mail'];
            $pass = $_POST['pass'];
        }
        $validacion = $this->usuarioModelo->comprobarLogin($mail, $pass);
        if ($validacion == false) {
              redireccionar('/login');
        } 
        else {
            session_start();
            $_SESSION['usuario'] = $validacion;
            $_SESSION['token_control'] = 1;
            redireccionar('/Inicio');
        }
    }


    public function vaciar(){
        session_start();
        session_unset();
        session_destroy();
        if(headers_sent()){
        return "<script>window.location.href=" . RUTA_URL . "</script>";    
        } else {
        redireccionar('/login');    
        }
        
    }

      
    

}
