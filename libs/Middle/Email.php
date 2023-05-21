<?php

namespace libs\Middle;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use libs\config;

class Email{

	public static function send($email,$title,$subject,$body){
		ob_start();
		try{
			$mail=new PHPMailer(true);
			// Configuración del servidor
			$mail->SMTPDebug=SMTP::DEBUG_SERVER;
			$mail->isSMTP();
			$mail->Host=config::email('host');
			$mail->SMTPAuth=true;
			$mail->Username=config::email('username');
			$mail->Password=config::email('password');
			$mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port=config::email('port');

			// Contenido
			$mail->setFrom(config::email('from'),$title); // Título del remitente
			$mail->addAddress($email); // Email del receptor
			$mail->isHTML(true);
			$mail->Subject=$subject;
			$mail->Body=utf8_encode($body);
			$done=$mail->send();
			ob_end_clean();
			return $done;
		}catch(Exception $ex){
			ob_end_clean();
			return false;
		}
	}

}

?>