<?php


class EnviarEmailControlador extends Controlador
{

    public function __construct()
    {
        //$this->iniciar();
        
    }

    public function enviarEmailSimple()
    {         
        $datos = [
            'email' => $_POST['email'],
            'asunto' => $_POST['asunto'],
            'mensaje' => $_POST['mensaje'],
            'controlador' => 'inicio' //el nombre del controlador a donde regresara, puede ser dinámico
         ];

        $nombreRemitente = 'Nombre Remitente Ejemplo'; // puede ser el $_SESSION['nombre'] del usuario logueado o lo que sea
        $emailRemitente = 'info@dataleanmakers.es'; // o $_SESSION['mail'] si no usa casilla de correo de Data Lean Makers
        $nombreDestinatario = 'Nombre Destinatario Ejemplo'; //puedes traer el nombre con el idPersona que viene por post.
        $emailDestinatario = $datos['email'];
        $asunto = $datos['asunto'];   
        $message = $datos['mensaje'];
        $attachment = '';        

        Email::enviarEmailDestinatario($nombreRemitente, $emailRemitente, $nombreDestinatario, $emailDestinatario,$asunto,$message,$attachment,$datos);
    }

        
}
