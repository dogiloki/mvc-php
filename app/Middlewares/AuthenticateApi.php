<?php

namespace app\Middlewares;

use libs\Auth\Models\AccessToken;
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
                return abort(401);
            }
            $access_token->last_activity=date('Y-m-d H:i:s');
            $access_token->save();
            Auth::login($access_token->tokenable(),$access_token);
            return $next($request);
        }
        return abort(401);
    }

}

?>