<?php

use libs\Router\Route;

Route::post('api/login','AuthController@login')->name('api-login');

// Usuarios
Route::get('api/users','UserController@show')->name('api-users-show');

?>