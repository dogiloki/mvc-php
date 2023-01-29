<?php

// Implementa el patrón singleton para mantener una única instancia y acceder a sus valores desde cuaquier sitio
namespace libs;

class Config{

	private $vars;
	private $positions;
	private $file;
	private static $instance=null;

	private function __construct($file=null){
		$this->vars=[];
		$this->positions=[];
		$this->file=$file;
		if($file==null){
			return;
		}
		$fp=fopen($file,"r");
		$count=0;
		while(!feof($fp)){
			$line=fgets($fp);
			$pos_key_index=0;
			$pos_key_end=strrpos($line,"=");
			$is_comment=substr(trim(strlen($line)<=0?"#":$line),0,1)=="#";
			if($pos_key_end>=0 && !$is_comment){
				$pos_value_index=$pos_key_end+1;
				$pos_value_end=(strlen($line)-$pos_value_index)-1;
				$key=substr($line,$pos_key_index,$pos_key_end);
				$value=substr($line,$pos_value_index,$pos_value_end);
				$this->positions[$count]=$key;
				$this->vars[$key]=$value;
			}
			$count++;
		}
		fclose($fp);
	}

	/**
	 * PENDIENTE
	**/
	// private function save(){
	// 	$fp=fopen($this->file,"w");
	// 	$count=0;
	// 	while(!feof($fp)){
	// 		$key=$this->positions[$count];
	// 		fputs($fp,$key."=".$this->get($key)??"")."\n";
	// 		$count++;
	// 	}
	// 	fclose($fp);
	// }

	public function set($key,$value){
		$this->vars[$key]=$value;
	}

	public function get($key){
		if(isset($this->vars[$key])){
			return $this->vars[$key];
		}
		return null;
	}

	public static function singleton($file=null){
		if(!isset(self::$instance)){
			$class=__CLASS__;
			self::$instance=new $class($file);
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