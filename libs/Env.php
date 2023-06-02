<?php

// Implementa el patrón singleton para mantener una única instancia y acceder a sus valores desde cuaquier sitio
namespace libs;

class Env{

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
		if(!file_exists($file)){
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
				$this->positions[$count]=trim($key);
				$this->vars[$key]=trim($value);
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

	public static function set($key,$value){
		$config=Env::singleton();
		$config->vars[$key]=$value;
	}

	public static function get($key){
		$config=Env::singleton();
		return $config->vars[$key]??null;
	}

	public static function singleton($file=null){
		if(self::$instance==null){
			self::$instance=new Env($file);
		}
		return self::$instance;
	}

}

?>