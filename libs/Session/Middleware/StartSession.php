<?php

namespace libs\Session\Middleware;

use libs\HTTP\Request;
use Closure;
use libs\Session\Session;
use libs\Middle\Middleware;

class StartSession extends Middleware{

    public function handle(Request $request, Closure $next){
        Session::singleton()->start();
        return $next($request);
    }

}

?>