<?php

use libs\Router\Route;

Route::get('/','AuthController@auth')->name('home');
Route::get('logout','AuthController@logout')->name('logout');
Route::get('admin/modules/{module}','AdminController@module')->name('admin-modules');
Route::get('admin','AuthController@admin')->name('admin');

?>