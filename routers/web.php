<?php

use models\User;
use models\Group;
use libs\DB\DB;
use libs\Middle\Auth;
use libs\Router\Route;
use libs\Middle\Faker;

$router->get('/','HomeController@index')->name('home');

$router->get('/user/{id?}','UserController@index')->name('user.index');
$router->post('/create/user','UserController@store')->name('user.store');
$router->get('/delete/user/{id}','UserController@delete')->name('user.delete');
$router->post('/update/user/{id}','UserController@update')->name('user.update');

$router->get('/group/{id?}','GroupController@index')->name('group.index');
$router->post('/create/group','GroupController@store')->name('group.store');
$router->get('/delete/group/{id}','GroupController@delete')->name('group.delete');
$router->post('/update/group/{id}','GroupController@update')->name('group.update');

$router->get('/login',function($request){
    Auth::login(User::find(1));
    dd(Auth::user());
})->name('login')->middleware('guest');

$router->get('/test/{var?}',function($request){
    dd(Auth::user());
})->name('test')->middleware('auth');

?>