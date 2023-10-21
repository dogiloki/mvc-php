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
    private $token;

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
        $this->user=$user;
        $this->token=$access_token;
        if(!Session::isStarted()){
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

    public function _attempt($credentials,$password){
        $user=$this->model_user::find(function($find)use($credentials){
            $find->where($credentials);
        });
        if($user){
            if(Secure::verifyPassword($password,$user->password)){
                Auth::login($user);
                return true;
            }
        }
        return false;
    }

    public function _check(){
        return ($this->user===null?Session::has($this->name_session):$this->user)!==null;
    }

    public function _logout(){
        $this->user=null;
        Session::forget($this->name_session);
        if(Config::session('driver')=='database'){
            $table=Config::session('database.table');
            DB::table($table)->update([
                'id_user'=>null,
            ])->where('id',Session::id())->execute();
        }
    }

    public function _user(){
        if($this->user!==null){
            return $this->user;
        }
        $id=Session::get($this->name_session);
        return $this->model_user::find($id);
    }

    public function _id(){
        return $this->user===null?Session::get($this->name_session):$this->user->{$this->user->primary_key};
    }

    public function _token(){
        return $this->token;
    }

}

?>