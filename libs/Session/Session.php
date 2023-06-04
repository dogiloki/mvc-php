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
        $this->name_session=Config::session('cookie.name');
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
            $this->started=true;
            return;
        }
        $this->session_id=Secure::random();
        $this->values['_token']=Session::token();
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
        $session_id=Secure::decrypt(Cookie::get($this->name_session));
        if(!$session_id){
            return false;
        }
        // Leer archivo de session
        if(Config::session('driver')=='file'){
            $path=Config::session('file.path');
            $file=$path."/".$session_id;
            if(file_exists($file)){
                $values_current=$this->values;
                $values_file=unserialize(file_get_contents($file))??[];
                $this->values=array_merge(
                    $values_file,
                    $this->values
                );
                if(count($values_current)>0){
                    foreach($this->values as $key=>$value){
                        if(!isset($values_current[$key])){
                            unset($this->values[$key]);
                        }
                    }
                }
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

    public function _regenerateToken(){
        Cookie::delete("CSFR_TOKEN");
        $this->put('_token',Session::token(true));
    }

    public function _token($regenerate=false){
		if(Cookie::exists('CSRF_TOKEN') && !$regenerate){
			return Cookie::get('CSRF_TOKEN');
		}
		$token=Secure::random();
		Cookie::set('CSRF_TOKEN',$token);
		return $token;
	}

    public function _regenerate(){
        $this->session_id=Secure::random();
        // Renombrar archivo de session
        if(Config::session('driver')=='file'){
            $path=Config::session('file.path');
            $file=$path."/".$this->session_id;
            $file_old=$path."/".Secure::decrypt(Cookie::get($this->name_session));
            if(file_exists($file_old)){
                rename($file_old,$file);
            }
        }
        $this->started=Cookie::set(
            $this->name_session,
            Secure::encrypt($this->session_id)
        );
        if($this->isStarted()){
            $this->put('_token',Session::token());
        }
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

    public function _payload(){
        return serialize($this->values);
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

    public function _pull($name){
        $value=$this->get($name);
        $this->forget($name);
        return $value;
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