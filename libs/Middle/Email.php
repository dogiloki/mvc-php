<?php

namespace libs\Middle;

require_once('vendor/phpmailer/phpmailer/src/Exception.php');
require_once('vendor/phpmailer/phpmailer/src/PHPMailer.php');
require_once('vendor/phpmailer/phpmailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email{

	public static function send($email,$title,$subject,$body){
		ob_start();
		try{
			$mail=new PHPMailer(true);
			// Configuración del servidor
			$mail->SMTPDebug=SMTP::DEBUG_SERVER;
			$mail->isSMTP();
			$mail->Host=env('EMAIL_HOST');
			$mail->SMTPAuth=true;
			$mail->Username=env('EMAIL_USERNAME');
			$mail->Password=env('EMAIL_PASSWORD');
			$mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port=env('EMAIL_PORT');

			// Contenido
			$mail->setFrom(env('EMAIL_USERNAME'),$title); // Título del remitente
			$mail->addAddress($email); // Email del receptor
			$mail->isHTML(true);
			$mail->Subject=$subject;
			$mail->Body=utf8_encode($body);
			$done=$mail->send();
			ob_end_clean();
			return $done;
		}catch(\Exception $ex){
			ob_end_clean();
			return false;
		}
	}

}

?>