<?php

use libs\Router\Router;
use models\User;
use models\Group;
use libs\DB\DB;
use libs\Middle\Auth;
use libs\Router\Route;
use libs\Middle\Faker;

$router=Router::singletong();

$router->get('/','HomeController@index')->name('home.index');

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
    return view('login');
})->name('login');

$router->get('/test/{var?}',function($request){
    dd($request);
})->name('test')->middleware('auth');

$router->controller();

?>