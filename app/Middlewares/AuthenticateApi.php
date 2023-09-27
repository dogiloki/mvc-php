<?php

namespace app\Middlewares;

use libs\Auth\AccessToken;
use libs\Middle\Middleware;
use libs\Auth\Auth;
use libs\HTTP\Request;

class AuthenticateApi extends Middleware{

    public function handle(Request $request, \Closure $next){
        $token=$request->bearerToken();
        $access_token=AccessToken::find(function($find)use($token){
            $find->where('token',$token);
        });
        if($access_token){
            if($access_token->hasExpired()){
                $access_token->delete();
                return abort(401,"Token has expired");
            }
            Auth::login($access_token->tokenable());
            return $next($request);
        }
        return abort(401);
    }

}

?>