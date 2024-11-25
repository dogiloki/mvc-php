<?php

namespace libs\Middle;

class Singleton{

	protected static $instances=[];

	public static function instance(){
		return self::$instances[get_called_class()]??null;
	}

	public static function __callStatic($method,$arguments){
		$method="_".$method;
        $instance=static::instance();
        if($instance==null){
        	$instance=get_called_class()::singleton();
        }
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$arguments);
        }
    }

	public static function singleton(){
		$instance=Singleton::$instances[get_called_class()]??null;
		if($instance===null){
			$instance=new static;
			Singleton::$instances[get_called_class()]=$instance;
		}
		return $instance;
	}

	public function __call($method,$arguments){
		return call_user_func_array([$this,"_".$method],$arguments);
	}

}

?>