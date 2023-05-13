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

    public function __construct(){
        $this->name_session=Middle::config('auth','session');
    }

    public static function __callStatic($method,$params){
        $ins=Auth::singleton();
        return $ins->$method($params);
    }

    public function login($user){
        Session::set($this->name_session,$user);
    }

    public function attempt($credentials, $password){
        $user=User::where($credentials)->first();
        if($user){
            if(Secure::verifyPassword($password,$user->password)){
                return true;
            }
        }
        return false;
    }

    public function check(){
        return Session::has($this->name_session);
    }

    public function logout(){
        Session::forget($this->name_session);
    }

}

?>