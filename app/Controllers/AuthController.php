<?php

namespace app\Controllers;

use app\Controllers\Controller;
use libs\HTTP\Request;
use libs\Middle\Secure;
use app\Models\User;

class AuthController extends Controller{
    
    public function register(Request $request){
        try{
            $user=new User();
            $user->name=$request->matricula;
            $user->email=$request->matricula;
            $user->password=Secure::encodePassword($request->password);
            $user->save();
            response(200);
        }catch(\Exception $ex){
            response(500);
        }
    }

    public function login(){
        
    }

}

?>