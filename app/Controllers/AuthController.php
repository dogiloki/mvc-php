<?php

namespace app\Controllers;

use app\Controllers\Controller;
use libs\HTTP\Request;
use libs\Middle\Secure;
use libs\Auth\Auth;
use app\Models\User;

class AuthController extends Controller{

    public function auth(){
        if(Auth::user()==null){
            return view('login');
        }else{
            return redirect(route('admin'));
        }
    }

    public function admin(){
        return view('admin.home');
    }

    public function login(Request $request){
        $user=User::visible('password')->where('registration',$request->user)->first();
        if($user==null || !Secure::verifyPassword($request->password,$user->password)){
            return response(404)->json([
                'message'=>'Acceso denegado'
            ]);
        }
        Auth::login($user);
        response(200);
    }

    public function logout(){
        Auth::logout();
        redirect(route('home'));
    }
    
}

?>