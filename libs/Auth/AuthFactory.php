<?php

namespace libs\Auth;

use libs\Session\Session;
use libs\Middle\Singleton;
use libs\Config;

abstract class AuthFactory extends Singleton{

    public $name_session;
    public $model_user;
    public $name_session_token;
    public $user;
    public $token;

    public function __construct(){
        $this->name_session=Config::auth('session.id_user');
        $this->name_session_token=Config::auth('session.remember_token');
        $this->model_user=Config::auth('web.model')??Config::auth('api.model');
    }

    abstract public function _attempt($credentials);
    abstract public function _login($user,$token=null);
    abstract public function _logout();
    abstract public function _user();
    abstract public function _id();

    public function _check(){
        return ($this->user===null)?(Session::has($this->name_session)):($this->user!==null);
    }

    public function _token(){
        return $this->token;
    }

}

?>