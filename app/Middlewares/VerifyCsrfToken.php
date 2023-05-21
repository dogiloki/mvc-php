<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\HTTP\Request;

class VerifyCsrfToken extends Middleware{

     public function handle(Request $request, \Closure $next){
          $csrf_token=$request->input('_token')??$request->header('X-CSRF-TOKEN')??$request->cookie()->get('CSRF_TOKEN')??null;
          if($csrf_token==null){
               Request::csrfToken();
               $request->session()->regenerate();
               return abort(401,"CSRF token not found");
          }
          if($csrf_token==$request->session()->get('_token')){
               return $next($request);
          }else{
               Request::csrfToken();
               $request->session()->regenerate();
               return abort(401,"CSRF token invalid");
          }
	}     

}

?>