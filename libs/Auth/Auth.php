<?php

namespace libs\Auth;

use libs\Session\Session;
use libs\Middle\Secure;
use libs\Middle\Singleton;
use libs\Auth\Models\AccessToken;
use libs\Config;
use libs\DB\DB;

class Auth extends Singleton{

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

}

?>