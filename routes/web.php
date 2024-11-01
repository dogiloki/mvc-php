<?php

use libs\Router\Route;

Route::get('/',function(){
    session()->put('message',[
        'status'=>'success',
        'data'=>'Hola'
    ]);
    return view('home');
})->name('home');

?>