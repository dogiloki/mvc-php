<?php

namespace libs\Auth;

use libs\Session\Session;
use libs\Auth\AuthFactory;
use libs\Config;

class AuthPAM extends AuthFactory{

    public function _attempt($credentials){
        if(pam_auth($credentials['user'],$credentials['password'])){
            Auth::login($credentials['user']);
        }
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
        Session::put($this->name_session,$user);
    }

    public function _logout(){
        $this->user=null;
        Session::forget($this->name_session);
    }

}

?>