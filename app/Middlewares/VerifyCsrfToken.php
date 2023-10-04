<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\HTTP\Request;
use libs\Session\Session;
use libs\Cookie\Cookie;

class VerifyCsrfToken extends Middleware{

     public function handle(Request $request, \Closure $next){
          $csrf_token=$request->input("_token")??null;
          if($request->method()=='GET'){
               return $next($request);
          }
          if($csrf_token==Session::get(Session::$key_csrf_token) && $csrf_token==Cookie::get('CSRF_TOKEN')){
               return $next($request);
          }else{
               $request->session()->regenerateToken();
               $request->session()->regenerate();
               abort(419,"CSRF token invalid");
          }
          return $next($request);
	}

}

?>