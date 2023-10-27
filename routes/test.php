<?php

use libs\Router\Route;
use libs\HTTP\Request;

Route::get('/test',function(Request $request){
    return json(app\Models\User::with('roles')->all());
})->name('test-get');

Route::post('/test',function(Request $request){
    return $request->name;
})->name('test-post');

?>