<?php

namespace libs;

class Config{

    public function __construct(){
        
    }

    public function __call($name,$arguments){
        return self::call($name,$arguments);
    }

    public static function __callStatic($name,$arguments){
        return self::call($name,$arguments);
    }

    public static function call($name,$keys){
        $keys=explode(".",$keys[0]??"");
        $filename="config/".$name.".php";
        $cfg=require($filename);
        if(file_exists($filename)){
            $value=$cfg;
            foreach($keys as $key){
                $value=$value[$key]??null;
            }
            $directory=$value;
            if(!is_array($directory) && !is_dir($directory) && $name=="filesystem" && !str_contains($directory,"{") && !str_contains($directory,".") && trim($directory," ")!=""){
                mkdir($directory,755,true);
            }
            return $value;
        }
        throw new \Exception("File not found: ".$filename);
    }

}

?>