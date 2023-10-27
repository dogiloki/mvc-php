<?php

use libs\Router\Route;
use libs\HTTP\Request;

Route::get('/',function(Request $request){
    return view('index');
})->name('home');


?>