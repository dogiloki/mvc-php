<?php

namespace libs\Cookie;

use libs\Config;

class Cookie{

    private static $instance=null;

    public static function __callStatic($method,$params){
        $method='_'.$method;
        $instance=Cookie::singleton();
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$params);
        }
    }

    public static function singleton(){
        if(self::$instance==null){
            self::$instance=new Cookie();
        }
        return self::$instance;
    }

    public function __construct(){
        
    }

    public function __call($method,$params){
        $method='_'.$method;
        if(method_exists($this,$method)){
            return call_user_func_array([$this,$method],$params);
        }
    }

    public function _set($name, $value, $time=null, $path=null, $domain=null, $secure=null, $httponly=null){
        $time??=Config::session('cookie.lifetime')*60;
        $path??=Config::session('cookie.path');
        $domain??=Config::session('cookie.domain')??parse_url($_SERVER['HTTP_HOST'],PHP_URL_HOST)??"/";
        $secure??=Config::session('cookie.secure');
        $httponly??=Config::session('cookie.httponly');
        if(setcookie($name,$value,time()+$time,$path,$domain,$secure,$httponly)){
            $_COOKIE[$name]=$value;
            return true;
        }
        return false;
    }

    public function _get($name){
        return $_COOKIE[$name]??null;
    }

    public function _delete($name){
        setcookie($name,"",time()-1,"/");
        unset($_COOKIE[$name]);
    }

    public function _exists($name){
        return isset($_COOKIE[$name]);
    }

    public function _all(){
        return $_COOKIE;
    }

}

?>