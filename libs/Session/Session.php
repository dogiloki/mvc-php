<?php

namespace libs\Session;

use libs\Config;
use libs\Middle\Secure;
use libs\Cookie\Cookie;

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
        if(!$instance->isStarted()){
            throw new \Exception("Session not started");
        }
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$params);
        }
    }

    private $started=false;
    private $name_session;
    private $session_id;
    private $values;

    private function __construct(){
        $this->values=[];
    }

    public function __call($method,$params){
        $method='_'.$method;
        if(method_exists($this,$method)){
            return call_user_func_array([$this,$method],$params);
        }
    }

    public function start(){
        if($this->sync()){
            return;
        }
        $this->session_id=Secure::random();
        $this->values['_token']=csrfToken();
        // Crear archivo de session
        if(Config::session('driver')=='file'){
            $path=Config::session('file.path');
            if(!is_dir($path)){
                mkdir($path,0777,true);
            }
            $file=$path."/".$this->session_id;
            file_put_contents($file,serialize($this->values));
        }
        $this->started=Cookie::set(
            $this->name_session,
            Secure::encrypt($this->session_id)
        );
    }

    private function sync(){
        $this->name_session=Config::session('cookie.name');
        $session_id=Secure::decrypt(Cookie::get($this->name_session));
        if(!$session_id){
            return false;
        }
        // Leer archivo de session
        if(Config::session('driver')=='file'){
            $path=Config::session('file.path');
            $file=$path."/".$session_id;
            if(file_exists($file)){
                $this->values=array_merge(
                    $this->values,
                    unserialize(file_get_contents($file))??[]
                );
                $this->session_id=$session_id;
                // Sobreescribir el archivo de session
                if(Config::session('driver')=='file'){
                    $path=Config::session('file.path');
                    $file=$path."/".$this->session_id;
                    file_put_contents($file,serialize($this->values));
                }
                return true;
            }
            $this->destroy();
            return false;
        }
    }

    public function _regenerate(){
        $this->destroy();
        $this->start();
    }

    public function _isStarted(){
        return $this->started;
    }

    public function _name(){
        return $this->name_session;
    }

    public function _id(){
        return $this->session_id;
    }

    public function _get($name){
        return $this->values[$name]??null;
    }

    public function _has($name){
        return isset($this->values[$name]);
    }

    public function _all(){
        return $this->values;
    }

    public function _put($name,$value){
        $this->values[$name]=$value;
        $this->sync();
    }

    public function _forget($name){
        unset($this->values[$name]);
        $this->sync();
    }

    public function _flush(){
        $this->values=[];
        $this->sync();
    }

    public function _destroy(){
        $this->values=[];
        // Eliminar archivo de session
        if(Config::session('driver')=='file'){
            $path=Config::session('file.path');
            $file=$path."/".$this->session_id;
            if($this->session_id!=null && file_exists($file)){
                unlink($file);
            }
        }
        Cookie::delete($this->name_session);
    }

}

?>