<?php

use app\models\User;
use libs\Middle\Secure;
use libs\Auth\Auth;
use libs\Middle\Session;
use libs\Middle\Log;
use libs\Middle\Storage;
use libs\Middle\Cookie;
use libs\Middle\Router;

$router->post('/test',function($request){
    echo $request->input('name');
})->name('test');

$router->get('/login',function($request){
    echo User::find(1)->createToken('token_auth')->token;
});

$router->get('/test',function($request){
    $user=Auth::user();
    echo $user->name;
})->middleware('auth_api');

?>