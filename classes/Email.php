<?php 

namespace Classes;

use PHPMailer\PHPMailer\PHPMailer;

class Email {

    public $email;
    public $nombre;
    public $token;

    public function __construct($email, $nombre, $token){
        $this->email = $email;
        $this->nombre = $nombre;
        $this->token = $token;

    }

    public function enviarConfirmacion(){

        //crea objeto
        $mail = new PHPMailer();
        $mail ->isSMTP();
        $mail ->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = 'ffb7f693d1151d';
        $mail->Password = 'cbee9e718cfc52';

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'confima tu cuenta';

        //set HTML

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . htmlspecialchars($this->nombre) . "</strong>, has creado tu cuenta en AppSalon, solo debes confirmarla presionando el siguiente enlace!</p>";
       $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['PROJECT_URL'] . "/confirmar?token=" . urlencode($this->token) . "'>Confirmar Cuenta</a></p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //enviar el email
        $mail->send();
        


    }

    public function enviarInstrucciones(){

         //crea objeto
        $mail = new PHPMailer();
        $mail ->isSMTP();
        $mail ->Host = $_ENV['EMAIL_HOST'];
        $mail->SMTPAuth = true;
        $mail->Port = $_ENV['EMAIL_PORT'];
        $mail->Username = $_ENV['EMAIL_USER'];
        $mail->Password = $_ENV['EMAIL_PASS'];

        $mail->setFrom('cuentas@appsalon.com');
        $mail->addAddress('cuentas@appsalon.com', 'AppSalon.com');
        $mail->Subject = 'restablece tu password';

        //set HTML

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';

        $contenido = "<html>";
        $contenido .= "<p><strong>Hola " . htmlspecialchars($this->nombre) . "</strong>, has solicitado reestablecer tu password, sigue el siguiente enlace!</p>";
        $contenido .= "<p>Presiona aquí: <a href='" .  $_ENV['PROJECT_URL'] . "/recuperar?token=" . urlencode($this->token) . "'>Recuperar password</a></p>";
        $contenido .= "</html>";

        $mail->Body = $contenido;

        //enviar el email
        $mail->send();


    }


};

?>