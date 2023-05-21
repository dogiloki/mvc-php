<?php

namespace app\Middlewares;
		
use libs\Middle\Middleware;
use libs\HTTP\Request;
use libs\Auth\Auth;
		
class Guest extends Middleware{

	public function handle(Request $request, \Closure $next){
        if(Auth::check()){
            return redirect(route('home'));
        }
        return $next($request);
    }
		
}
		
?>