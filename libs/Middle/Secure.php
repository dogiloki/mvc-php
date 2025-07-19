<?php

namespace libs\Middle;

use libs\Config;
use phpseclib3\Crypt\AES;
use libs\Middle\Log;

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

	public static function randomBase64($size=32){
		return base64_encode(self::random($size));
	}

	public static function hash($text=null,$binary=false){
		$text??=uniqid(rand(),true);
		return hash('sha256',$text,$binary);
	}

	public static function hashHmac($text=null,$binary=false){
		$key=Config::app('key');
		return hash_hmac('sha256',$text??"",$key,$binary);
	}

	public static function encryptFileStream($input_file,$output_file,$key=null){
		try{
			$key=Secure::hashHmac($key,true);
			$aes=new AES('ctr');
			$aes->setKey($key);

			$handle_in=fopen($input_file,'rb');
			$handle_out=fopen($output_file,'wb');

			$iv=random_bytes($aes->getBlockLength()>>3);
			$aes->setIV($iv);

			fwrite($handle_out,$iv);

			while(!feof($handle_in)){
				$plaint_text=fread($handle_in,65536);
				$cipher_text=$aes->encrypt($plaint_text);
				fwrite($handle_out,$cipher_text);
			}

			fclose($handle_in);
			fclose($handle_out);

			return true;
		}catch(\Exception $ex){
			Log::error("Error encrypting file: ".$ex->getMessage());
			return false;
		}
	}
	
	public static function decryptFileStream($input_file,$output_file,$key=null){
		try{
			$key=Secure::hashHmac($key,true);
			$aes=new AES('ctr');
			$aes->setKey($key);

			$handle_in=fopen($input_file,'rb');
			$handle_out=fopen($output_file,'wb');

			if(!$handle_in || !$handle_out){
				return false;
			}

			$vi_length=$aes->getBlockLength()>>3;
			$iv=fread($handle_in,$vi_length);
			if($iv===false || strlen($iv)!==$vi_length){
				fclose($handle_in);
				fclose($handle_out);
				Log::error("Error reading IV from file: ".$input_file);
				return false;
			}
			$aes->setIV($iv);

			while(!feof($handle_in)){
				$cipher_text=fread($handle_in,65536);
				if($cipher_text===false){
					fclose($handle_in);
					fclose($handle_out);
					Log::error("Error reading cipher text from file: ".$input_file);
					return false;
				}
				$plaint_text=$aes->decrypt($cipher_text);
				fwrite($handle_out,$plaint_text);
			}

			fclose($handle_in);
			fclose($handle_out);
			Log::info("File decrypted successfully: ".$input_file);

			return true;
		}catch(\Exception $ex){
			Log::error("Error encrypting file: ".$ex->getMessage());
			return false;
		}
	}

	public static function encryptNotBase64($text,$key=null){
		return self::encrypt($text,$key,false);
	}
	
	public static function encrypt($text,$key=null,$base64=true){
		$key=Secure::hashHmac($key,true);
		if($text==null || $key==null){
			return null;
		}
		$iv=openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		$code=$iv.openssl_encrypt($text,'aes-256-cbc',$key,OPENSSL_RAW_DATA,$iv);
		return $base64?base64_encode($code):$code;
	}

	public static function decryptNotBase64($text,$key=null){
		return self::decrypt($text,$key,false);
	}

	public static function decrypt($text,$key=null,$base64=true){
		$key=Secure::hashHmac($key,true);
		if($text==null || $key==null){
			return null;
		}
		if($base64){
			$text=base64_decode($text);
		}
		$iv=substr($text,0,openssl_cipher_iv_length('aes-256-cbc'));
		return openssl_decrypt(substr($text,openssl_cipher_iv_length('aes-256-cbc')),'aes-256-cbc',$key,OPENSSL_RAW_DATA,$iv);
	}
	
}

?>