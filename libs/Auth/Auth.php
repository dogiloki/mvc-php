<?php

namespace libs\Auth;

use libs\Session\Session;
use libs\Middle\Secure;
use libs\Auth\Models\AccessToken;
use libs\Config;
use libs\DB\DB;

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
    private $user;

    public function __construct(){
        $this->name_session='auth';
        $this->name_session_token='token';
        $this->model_user=Config::auth('web.model');
    }

    public static function __callStatic($method,$params){
        $method='_'.$method;
		$instance=Auth::singleton();
		if(method_exists($instance,$method)){
			return call_user_func_array([$instance,$method],$params);
		}
    }

    public function _login($user,$access_token=null){
        if($user==null){
            return;
        }
        if(!Session::isStarted()){
            $this->user=$user;
            if($access_token!==null){
                $this->user->token=$access_token;
            }
            return;
        }
        Session::put($this->name_session,$user->{$user->primary_key});
        if(Config::session('driver')=='database'){
            $table=Config::session('database.table');
            DB::table($table)->update([
                'id_user'=>$user->{$user->primary_key},
            ])->where('id',Session::id())->execute();
        }
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
        return Session::isStarted()?Session::has($this->name_session):$this->user!==null;
    }

    public function _logout(){
        if(!Session::isStarted()){
            $this->user=null;
            return;
        }
        Session::forget($this->name_session);
        if(Config::session('driver')=='database'){
            $table=Config::session('database.table');
            DB::table($table)->update([
                'id_user'=>null,
            ])->where('id',Session::id())->execute();
        }
    }

    public function _user(){
        if(!Session::isStarted()){
            return $this->user;
        }
        $id=Session::get($this->name_session);
        return $this->model_user::find($id);
    }

    public function _id(){
        return Session::isStarted()?Session::get($this->name_session):$this->user->{$this->user->primary_key};
    }

}

?>