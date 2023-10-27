<?php

namespace libs\Middle;

class Singleton{

	protected static $instance=null;

	public static function __callStatic($method,$arguments){
		$method="_".$method;
        $instance=static::singleton();
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$arguments);
        }
    }

	public static function singleton(){
		if(static::$instance===null){
			static::$instance=new static();
		}
		return static::$instance;
	}

	protected function __construct(){
			
	}

}

?>