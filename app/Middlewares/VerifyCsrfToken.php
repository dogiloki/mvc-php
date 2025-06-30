<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\HTTP\Request;
use libs\Session\Session;
use libs\Cookie\Cookie;

class VerifyCsrfToken extends Middleware{

     // Validar token CSRF con excepción de métodos GET y ruta que inicien con api
     public function handle(Request $request, \Closure $next){
          $csrf_token=$request->input("_token")??null;
          if($request->method()=='GET' || strpos($request->path(),'api')===0 || strpos($request->path(),'/api')===0){
               return $next($request);
          }
          if($csrf_token==Session::get(Session::$key_csrf_token) && $csrf_token==Cookie::get('CSRF_TOKEN')){
               return $next($request);
          }else{
               $request->session()->regenerateToken();
               $request->session()->regenerate();
               return abort(419,"CSRF token invalid");
          }
          return $next($request);
	}

}

?>