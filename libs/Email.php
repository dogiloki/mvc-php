<?php

namespace libs;

require_once('vendor/phpmailer/phpmailer/src/Exception.php');
require_once('vendor/phpmailer/phpmailer/src/PHPMailer.php');
require_once('vendor/phpmailer/phpmailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use libs\Config;

class Email{

	public static function send($email,$title,$subject,$body){
		ob_start();
		try{
			$mail=new PHPMailer(true);
			// Configuración del servidor
			$mail->SMTPDebug=SMTP::DEBUG_SERVER;
			$mail->isSMTP();
			$mail->Host=Config::get('EMAIL_HOST');
			$mail->SMTPAuth=true;
			$mail->Username=Config::get('EMAIL_ADDRESS');
			$mail->Password=Config::get('EMAIL_PASSWORD');
			$mail->SMTPSecure=PHPMailer::ENCRYPTION_STARTTLS;
			$mail->Port=Config::get('EMAIL_PORT');

			// Contenido
			$mail->setFrom(Config::get('EMAIL_ADDRESS'),$title); // Título del remitente
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