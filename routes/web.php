<?php

use app\Models\User;
use libs\Auth\Auth;

$router->get('/',function(){
    return view('index');
})->name('home')->middleware('auth','verify_email');

$router->get('/login/{id?}',function($request){
    $user=User::find($request->input('id'));
    dd($user);
    Auth::login($user);
    return redirect(route('home'));
})->name('login')->middleware('guest');

$router->get('/logout',function(){
    Auth::logout();
    return redirect(route('login'));
})->name('logout')->middleware('auth');

$router->get('/verify_email',function(){
    Auth::user()->update(['verify_email_at'=>date('Y-m-d H:i:s')]);
    return redirect(route('home'));
})->name('verify_email')->middleware('auth');

?>