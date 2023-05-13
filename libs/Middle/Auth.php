<?php

namespace libs\Middle;

use libs\Session;
use libs\Middle\Secure;
use libs\Middle\Middle;

class Auth{

    public static function singleton(){
        if(self::$instance==null){
            self::$instance=new Auth();
        }
        return self::$instance;
    }

    private static $instance;
    private $name_session;
    private $model_user;

    public function __construct(){
        $this->name_session=Middle::config('auth','session');
        $this->model_user=Middle::config('auth','model');
    }

    public static function __callStatic($method,$params){
        $method='_'.$method;
		$instance=Auth::singleton();
		if(method_exists($instance,$method)){
			return call_user_func_array([$instance,$method],$params);
		}
    }

    public function _login($user){
        Session::set($this->name_session,$user);
    }

    public function _attempt($credentials, $password){
        $user=$this->model_user::find(function($find)use($credentials){
            $find->where($credentials);
        });
        if($user){
            if(Secure::verifyPassword($password,$user->password)){
                return true;
            }
        }
        return false;
    }

    public function _check(){
        return Session::has($this->name_session);
    }

    public function _logout(){
        Session::forget($this->name_session);
    }

}

?>