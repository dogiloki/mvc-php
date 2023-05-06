<?php

namespace libs;

class Session{

    private static $instance=null;

    private function __construct(){
        session_unset();
        session_start();
    }

    public static function singleton(){
        if(self::$instance==null){
            self::$instance=new Session();
        }
        return self::$instance;
    }

    public static function put($key,$value=null){
        $session=self::singleton();
        $_SESSION[$key]=$value;
    }

    public static function get($key){
        $session=self::singleton();
        return $_SESSION[$key]??null;
    }

    public static function has($key){
        $session=self::singleton();
        return isset($_SESSION[$key]);
    }

    public static function all(){
        $session=self::singleton();
        return $_SESSION;
    }

    public static function pull($key){
        $session=self::singleton();
        $value=$_SESSION[$key]??null;
        unset($_SESSION[$key]);
        return $value;
    }

    public static function forget($key){
        $session=self::singleton();
        unset($_SESSION[$key]);
    }

    public static function flush(){
        $session=self::singleton();
        session_unset();
    }

    public static function destroy(){
        $session=self::singleton();
        self::$instance=null;
        session_destroy();
    }

}

?>