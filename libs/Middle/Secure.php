<?php

namespace libs\Middle;

use libs\Config;

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
		$text??=uniqid(rand(),true);
		return hash('sha256',$text);
	}

	public static function encryptNotBase64($text,$key=null){
		return self::encrypt($text,$key,false);
	}
	
	public static function encrypt($text,$key=null,$base64=true){
		$key??=Config::app('key');
		if($text==null || $key==null){
			return null;
		}
		$iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		$code=$iv.openssl_encrypt($text,'aes-256-cbc',$key,0,$iv);
		return $base64?base64_encode($code):$code;
	}

	public static function decryptNotBase64($text,$key=null){
		return self::decrypt($text,$key,false);
	}

	public static function decrypt($text,$key=null,$base64=true){
		$key??=Config::app('key');
		if($text==null || $key==null){
			return null;
		}
		if($base64){
			$text=base64_decode($text);
		}
		$iv=substr($text,0,openssl_cipher_iv_length('aes-256-cbc'));
		return openssl_decrypt(substr($text,openssl_cipher_iv_length('aes-256-cbc')),'aes-256-cbc',$key,0,$iv);
	}
	
}

?>