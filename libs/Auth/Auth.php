<?php

namespace libs\Auth;

use libs\Middle\Secure;
use libs\Auth\Models\AccessToken;
use libs\Config;
use libs\DB\DB;
use libs\Auth\AuthDB;
use libs\Auth\AuthPAM;
use libs\Middle\Singleton;

class Auth extends Singleton{

    public $extends=null;

    public static function __callStatic($method,$arguments){
		$method="_".$method;
        $instance=Singleton::$instances[get_called_class()]??null;
        if($instance==null){
        	$instance=get_called_class()::singleton();
        }
        $instance=new ($instance->extends())();
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$arguments);
        }
    }

    public function __construct(){
        $this->extends=config()->auth('pam_auth')?AuthPAM::class:AuthDB::class;
    }

    public function extends(){
        return $this->extends;
    }

}

?>