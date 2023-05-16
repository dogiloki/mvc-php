<?php

namespace libs\Middle;

use libs\Config;

class Session{

    private static $instance=null;

    public static function singleton(){
        if(self::$instance==null){
            self::$instance=new Session();
        }
        return self::$instance;
    }

    public static function __callStatic($method,$params){
        $method='_'.$method;
        $instance=Session::singleton();
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$params);
        }
    }

    private $name_session;
    private $session_id;

    private function __construct(){
        session_unset();
        session_name(Config::session('name'));
        $this->name_session=session_name();
        $this->session_id=session_id();
        session_start([
            'cookie_lifetime'=>Config::session('lifetime')*60,
            'cookie_path'=>Config::session('path'),
            'cookie_domain'=>Config::session('domain')??parse_url($_SERVER['HTTP_HOST'],PHP_URL_HOST),
            'cookie_secure'=>Config::session('secure'),
            'cookie_httponly'=>Config::session('httponly'),
            'cookie_samesite'=>Config::session('samesite')
        ]);
    }

    public function __call($method,$params){
        $method='_'.$method;
        if(method_exists($this,$method)){
            return call_user_func_array([$this,$method],$params);
        }
    }

    public function _name(){
        return $this->name_session;
    }

    public function _id(){
        return $this->session_id;
    }

    public function _regenerate(){  
        $this->session_id=session_regenerate_id(true);
    }

    public function _put($key,$value=null){
        $_SESSION[$key]=$value;
    }

    public function _get($key){
        return $_SESSION[$key]??null;
    }

    public function _has($key){
        return isset($_SESSION[$key]);
    }

    public function _all(){
        return $_SESSION;
    }

    public function _pull($key){
        $value=$_SESSION[$key]??null;
        unset($_SESSION[$key]);
        return $value;
    }

    public function _forget($key){
        unset($_SESSION[$key]);
    }

    public function _flush(){
        session_unset();
    }

    public function _destroy(){
        self::$instance=null;
        session_destroy();
    }
    
    public function _encode(){
        return session_encode();
    }

    public function _payload(){
        return base64_encode($this->encode());
    }

}

?>