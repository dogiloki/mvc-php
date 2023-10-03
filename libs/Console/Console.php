<?php

namespace libs\Console;

class Console{

    public static function __callStatic($name,$arguments){
        $instace=new Console();
        if(method_exists($instace,$name)){
			return call_user_func_array([$instace,$name],$arguments);
		}
    }

    public function ask($text){
        return readline($text);
    }

    public function error($text){
        print("\033[31m".$text."\033[0m")."\n";
        return $this;
    }

    public function warning($text){
        print("\033[33m".$text."\033[0m")."\n";
        return $this;
    }

    public function success($text){
        print("\033[32m".$text."\033[0m")."\n";
        return $this;
    }

    public function info($text){
        print($text."\n");
        return $this;
    }

    public function dd($text){
        print_r($text."\n");
        return $this;
    }

    public function exit($code=null){
        exit($code);
    }

}

?>