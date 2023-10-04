<?php

use libs\HTTP\Request;

$router->get('/',function(Request $request){
    return view('index');
})->name('home')->middleware('auth');


?>