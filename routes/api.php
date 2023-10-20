<?php

use app\Models\User;
use libs\Auth\Auth;

$router->get('/user',function($request){
    return json(User::find(1)->createToken('auth_token'));
})->name('api-user');

$router->get('/',function($request){
    Auth::user();
    $user=Auth::user();
    return json(Auth::user());
})->middleware('auth_api')->name('api-auth');

?>