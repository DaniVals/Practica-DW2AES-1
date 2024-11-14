<?php
    use PHPMailer\PHPMailer\PHPMailer;

    function enviarEmail($destinatary, $origin, $subject, $msgBody) {
        
        require "vendor/autoload.php";
        $mail = new PHPMailer();
        $mail->IsSMTP();

        $mail->SMTPDebug  = 0; 							
        $mail->SMTPAuth   = true;
        $mail->SMTPSecure = "tls";                 
        $mail->Host       = "sandbox.smtp.mailtrap.io";    
        $mail->Port       = 2525;

        //PARA CAMBIAR
        $mail->Username   = "d3da94efb613fc"; 
	    $mail->Password   = "15517b854da168";

        $mail->SetFrom($origin, 'Test');
        $mail->Subject    = $subject;
	    $mail->MsgHTML($msgBody);
	    $mail->AddAddress($destinatary, "Test");

        $resul = $mail->Send();
        if(!$resul) {
            echo "Error" . $mail->ErrorInfo;
        }
        else {
            echo "Enviado";
        }
    }

?>