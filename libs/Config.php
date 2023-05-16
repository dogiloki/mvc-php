<?php

namespace libs;

class Config{

    public static function __callStatic($name,$keys){
        $filename="config/".$name.".php";
        $cfg=require($filename);
        if(file_exists($filename)){
            $value=$cfg;
            foreach($keys as $key){
                $value=$value[$key];
            }
            return $value;
        }
        throw new \Exception("File not found: ".$filename);
    }

}

?>