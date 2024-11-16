<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($destinatary, $origin, $subject, $msgBody) {

    try {

        require "vendor/autoload.php";
        $mail = new PHPMailer();
        $mail->IsSMTP();

        $mail->SMTPDebug  = 0; 							
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = "tls";                 
        $mail->Host       = "sandbox.smtp.mailtrap.io";    
        $mail->Port       = 2525;

        //PARA CAMBIAR
        $mail->Username = 'c281f991d9b8e7';
        $mail->Password = 'c935a15b3b03fe';

        $mail->SetFrom($origin, 'Test');
        $mail->Subject    = $subject;
        $mail->MsgHTML($msgBody);
        $mail->AddAddress($destinatary, "Test");

        $resul = $mail->Send();
        if(!$resul) {
            return false;
        }
        else {
            // echo "Enviado";
            return true;
        }
    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
        echo "Error: {$e->errorMessage()}";
    }
}
?>
