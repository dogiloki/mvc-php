<?php

use libs\Router\Route;

Route::get('/',function(){
    return view('home');
})->name('home');

?>