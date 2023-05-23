<?php

use libs\HTTP\Request;

$router->get('/',function(Request $request){
    $request->session()->regenerate();
    return view('index');
})->name('home');


?>