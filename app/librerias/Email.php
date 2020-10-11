<?php

    /**
     * Clase Email :
     * 
     * Clase para el envío de emails
     * 
     * @autor Jenny Ruiz
     * 
     * @version 1.0 - Versión inicial - Octubre 10 de 2020
     * 
     * @copyright Data Lean Makers
     * 
     */

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

        public static function enviarEmailDestinatario($nombreRemitente, $emailRemitente, $nombreDestinatario='', $emailDestinatario,$asunto,$message,$attachment,$datos='') 
        {

            try {
                $mail = new PHPMailer;          
                $mail->IsSMTP(); // enable SMTP
                
                $mail->SMTPAuth = true; // authentication enabled
                $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
                );          
                $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
                $mail->Host = "smtp.gmail.com";
                $mail->Port = 465; // or 587
                $mail->IsHTML(true);
                $mail->Username = "info@dataleanmakers.es";
                $mail->Password = "Bobedano2019$"; 
                $mail->SetFrom($emailRemitente,$nombreRemitente); 
                $mail->Subject = utf8_decode($asunto);
                $mail->Body = $message;               
                $mail->AddAddress($emailDestinatario,$nombreDestinatario);
                
                if ($attachment !='') {
                    $mail->AddStringAttachment($attachment, 'factura.pdf');
                }
                
                if(!$mail->Send()) {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                    $variable = '<script type="text/javascript">window.location.href="'.RUTA_URL.'/'.$datos['controlador'].'/1"</script>';                     
                } else {                                        
                    $variable = '<script type="text/javascript">window.location.href="'.RUTA_URL.'/'.$datos['controlador'].'/2"</script>';
                }                         
                echo $variable;
            } catch(Exception $exception){                                                  
                return $exception->getMessage();                                 
            }   

        }


          
    }