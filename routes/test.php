<?php

use app\Models\User;
use libs\Middle\Secure;
use libs\Auth\Auth;
use libs\Middle\Session;
use libs\Middle\Log;
use libs\Middle\Storage;
use libs\Middle\Cookie;
use libs\Middle\Router;
use libs\Middle\Faker;
use libs\Middle\Models\GlobalVar;

$router->get('/login',function($request){
    Auth::login(User::find(1));
})->name('login');

$router->get('/logout',function($request){
    Auth::logout();
})->name('logout');

$router->get('/test',function($request){
    $data=app\Models\User::where('id','5')->or()->whereRaw('id=:1',[":1"=>1])->get();
    dd($data);
})->name('test-get');

$router->post('/test',function($request){
    dd($request->name);
})->name('test-post');

?>