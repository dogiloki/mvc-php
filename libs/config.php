<?php

// Implementa el patrón singleton para mantener una única instancia y acceder a sus valores desde cuaquier sitio
namespace libs;

class Config{

	private $vars;
	private static $instance=null;

	private function __construct(){
		$this->vars=[];
	}

	public function set($name,$value){
		if(!isset($this->vars[$name])){
			$this->vars[$name]=$value;
		}
	}

	public function get($name){
		if(isset($this->vars[$name])){
			return $this->vars[$name];
		}
	}

	public static function singleton(){
		if(!isset(self::$instance)){
			$class=__CLASS__;
			self::$instance=new $class;
		}
		return self::$instance;
	}

}

/*
Ejemplo de uso
$config=Config::singleton();
$config->set('nombre','Juan');
echo $config->get('nombre');
*/

?>