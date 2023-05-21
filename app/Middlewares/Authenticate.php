<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\Auth\Auth;
use libs\HTTP\Request;

class Authenticate extends Middleware{

    public function handle(Request $request, \Closure $next){
        if(!Auth::check()){
            echo "da";
            return redirect(route('login'));
        }
        return $next($request);
    }

}

?>