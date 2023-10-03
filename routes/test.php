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
    dd(User::where('id',1)->get());
})->name('login');

$router->get('/test',function($request){
    dd(User::find(1)->name);
})->name('test');

?>