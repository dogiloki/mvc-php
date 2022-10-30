<?php

class Secure{

	public static function encodePassword($password){
		return password_hash($password,PASSWORD_DEFAULT);
	}

	public static function verifyPassword($password,$password_coded){
		return password_verify($password,$password_coded);
	}

	public static function generateToken($size=64){
		return bin2hex(random_bytes($size));
	}
	
}

?>