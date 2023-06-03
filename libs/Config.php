<?php

namespace libs;

class Config{

    public const ACTIVE_REPLACE=true;

    public static function __callStatic($name,$keys){
        $keys=explode(".",$keys[0]);
        $filename="config/".$name.".php";
        $cfg=require($filename);
        if(file_exists($filename)){
            $value=$cfg;
            foreach($keys as $key){
                $value=$value[$key]??null;
            }
            return Config::ACTIVE_REPLACE?str_replace("/","\\",$value):$value;
        }
        throw new \Exception("File not found: ".$filename);
    }

}

?>