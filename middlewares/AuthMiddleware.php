<?php

namespace middlewares;

use libs\Middle\Middle;
use libs\Middle\Auth;

class AuthMiddleware extends Middle{

    public function redirectTo($action, $params=[]){
        if(Auth::check()){
            return $action($params);
        }
        return redirect(route('login'));
    }
}

?>