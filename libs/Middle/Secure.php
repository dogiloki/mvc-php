<?php

namespace libs\Middle;

class Secure{

	public static function encodePassword($text){
		return password_hash($text,PASSWORD_DEFAULT);
	}

	public static function verifyPassword($text,$text_coded){
		return password_verify($text,$text_coded);
	}

	public static function random($size=32){
		return substr(str_replace(["/","+","="],"",base64_encode(random_bytes($size))),0,$size);
		//return bin2hex(openssl_random_pseudo_bytes($size));
	}

	public static function hash($text=null){
		$text??=uniqid(uniqid());
		return hash('sha256',$text);
	}
	
}

?>