<?php

use app\Models\User;
use libs\Middle\Secure;
use libs\Auth\Auth;
use libs\Middle\Session;
use libs\Middle\Log;
use libs\Middle\Storage;
use libs\Middle\Cookie;
use libs\Middle\Router;

$router->get('/login',function($request){
    Auth::login(User::find(1));
})->name('login');

$router->get('/logout',function($request){
    Auth::logout();
})->name('logout');

$router->get('/test',function($request){
    dd(User::visible("name","email")->each(function($model){
        $model->name_full=$model->name." ".$model->email;
    })->with([
        'saludo'=>"Bienvenido"
    ])->find(1));
})->name('test-get');

$router->post('/test',function($request){
    dd($request->name);
})->name('test-post');

?>