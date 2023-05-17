<?php

namespace libs\Middle;

use libs\Config;

class Log{

    public static function write($message,$file=null){
        $file??=Config::filesystem('logs.file');
        $handler=fopen($file,"a");
        fwrite($handler,$message."\n");
        fclose($handler);
    }

    public static function channel($channel,$message){
        $path=Config::filesystem('logs.channels.'.$channel.'.path');
        if(!is_dir($path)){
            mkdir($path,0777,true);
        }
        $file=$path."/".date("Y-m-d").".log";
        Log::write("[".date("Y-m-d H:i:s")."] [".$channel."] ".$message,$file);
    }

    public static function info($message){
        Log::write("[".date("Y-m-d H:i:s")."] [INFO] ".$message);
    }

    public static function error($message){
        Log::write("[".date("Y-m-d H:i:s")."] [ERROR] ".$message);
    }

    public static function warning($message){
        Log::write("[".date("Y-m-d H:i:s")."] [WARNING] ".$message);
    }

    public static function debug($message){
        Log::write("[".date("Y-m-d H:i:s")."] [DEBUG] ".$message);
    }

    public static function notice($message){
        Log::write("[".date("Y-m-d H:i:s")."] [NOTICE] ".$message);
    }

    public static function critical($message){
        Log::write("[".date("Y-m-d H:i:s")."] [CRITICAL] ".$message);
    }

    public static function alert($message){
        Log::write("[".date("Y-m-d H:i:s")."] [ALERT] ".$message);
    }

    public static function emergency($message){
        Log::write("[".date("Y-m-d H:i:s")."] [EMERGENCY] ".$message);
    }

}

?>