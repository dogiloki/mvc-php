<?php

class Middle{

    public static function singleton(){
        dd("das");
        if(self::$instance==null){
            self::$instance=new Middle();
        }
        return self::$instance;
    }

    public static function config(){
        $keys=func_get_args();
        $ins=Middle::singleton();
        $cfg=$ins->cfg;
        foreach($keys as $key){
            $cfg=$cfg[$key];
        }
        return $cfg;
    }

    private static $instance;
    private $cfg;

    public function  __construct(){
        $this->cfg=include 'config/middle.php';
    }

}

?>