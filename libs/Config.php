<?php

namespace libs;

class Config{

    public function __construct(){
        
    }

    public function __call($name,$arguments){
        return self::call($name,$arguments);
    }

    public static function __callStatic($name,$keys){
        return self::call($name,$keys);
    }

    public static function call($name,$keys){
        $keys=explode(".",$keys[0]);
        $filename="config/".$name.".php";
        $cfg=require($filename);
        if(file_exists($filename)){
            $value=$cfg;
            foreach($keys as $key){
                $value=$value[$key]??null;
            }
            return $value;
        }
        throw new \Exception("File not found: ".$filename);
    }

}

?>