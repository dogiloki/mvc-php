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

    public static function instance(){
	    return new ((new self())->extends())();
	}

    public $extends=null;

    public function __construct(){
        $this->extends=config()->auth('pam_auth')?AuthPAM::class:AuthDB::class;
    }

    public function extends(){
        return $this->extends;
    }

}

?>