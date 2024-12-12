<?php

namespace libs\Auth;

use libs\Session\Session;
use libs\Middle\Secure;
use libs\Auth\AuthFactory;
use libs\DB\DB;
use libs\Config;

class AuthDB extends AuthFactory{

    public function _attempt($credentials){
        $user=$this->model_user::visible('id','password')->find(function($find)use($credentials){
            foreach($credentials as $key=>$value){
                if($key=="password"){
                    $find->select('id');
                    $find->select($key);
                    continue;
                }
                $find->select($key);
                $find->where($key,$value);
            }
        });
        if($user==null){
            return false;
        }
        if(Secure::verifyPassword($credentials['password'],$user->password)){
            Auth::login($user);
            return true;
        }
        return false;
    }

    public function _login($user,$token=null){
        if($user==null){
            return;
        }
        $this->user=$user;
        $this->token=$token;
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

}

?>