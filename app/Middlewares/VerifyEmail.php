<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\HTTP\Request;
use libs\Auth\Auth;

class VerifyEmail extends Middleware{

    public function handle(Request $request, \Closure $next){
        $user=Auth::user();
        if($user==null){
            return abort(401,"Not email verified");
        }
        return $user->verify_email_at==null?abort(401,"Not email verified"):$next($request);
    }

}

?>