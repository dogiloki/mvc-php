<?php

namespace libs\Middle;

class Secure{

	public static function encodePassword($text){
		return password_hash($text,PASSWORD_DEFAULT);
	}

	public static function verifyPassword($text,$text_coded){
		return password_verify($text,$text_coded);
	}

	public static function token($size=64){
		return bin2hex(random_bytes($size));
		//return bin2hex(openssl_random_pseudo_bytes($size));
	}

	public static function hash($text){
		return hash('sha256',$text);
	}
	
}

?>