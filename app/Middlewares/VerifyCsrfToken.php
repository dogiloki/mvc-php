<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\HTTP\Request;

class VerifyCsrfToken extends Middleware{

     public function handle(Request $request, \Closure $next){
          $csrf_token=$request->input('_token')??$request->header('X-CSRF-TOKEN');
          if($csrf_token==null){
               return abort(401,"CSRF token not found");
          }
          return $csrf_token==csrfToken()?$next($request):abort(401,"CSRF token invalid");
	}     

}

?>