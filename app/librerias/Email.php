<?php

    require '../public/librerias/PHPMailer-master/src/Exception.php';
    require '../public/librerias/PHPMailer-master/src/PHPMailer.php';
    require '../public/librerias/PHPMailer-master/src/SMTP.php'; 
        
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    class Email {

        /**
         * Función genérica para el envío de email con la librería PHPMailer
         * 
         * Se pueden hacer envío a uno o varios destinatarios, con o sin un fichero adjunto,
         * un asunto y cuerpo de mensaje, así como un array de datos que se requiera
         * 
         * @param array emailDestinatario
         * @param array datos
         * @return script headerlocation
         */

    

          
    }