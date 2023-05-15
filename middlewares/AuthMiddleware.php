<?php

namespace middlewares;

use libs\Middle\Middle;
use libs\Middle\Auth;

class AuthMiddleware extends Middle{

    public function redirectTo($next){
        if(Auth::check()){
            return $next();
        }
        return redirect(route('login'));
    }
    
}

?>