<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\HTTP\Request;
use libs\Session\Session;

class VerifyCsrfToken extends Middleware{

     public function handle(Request $request, \Closure $next){
          $csrf_token=$request->input(Session::$key_csrf_token)??$request->header('X-CSRF-TOKEN')??$request->cookie()->get('CSRF_TOKEN')??null;
          if($csrf_token==null){
               if($request->method()=='GET'){
                    $request->session()->regenerateToken();
                    $request->session()->regenerate();
                    reload();
               }
               abort(401,"CSRF token invalid");
          }
          if($csrf_token!=$request->session()->get(Session::$key_csrf_token)){
               if($request->method()=='GET'){
                    $request->session()->regenerateToken();
                    $request->session()->regenerate();
                    reload();
               }
               abort(401,"CSRF token invalid");
          }
          return $next($request);
	}     

}

?>