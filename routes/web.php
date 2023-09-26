<?php

use libs\HTTP\Request;
use libs\Auth\Auth;
use app\Models\User;
use libs\Session\Session;

$router->get('/',function(Request $request){
    return view('index');
})->name('home');


?>