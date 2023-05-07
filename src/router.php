<?php

use libs\Router;

$router=Router::singletong();

$router->get('/','HomeController@index')->name('home.index');

$router->get('/user','UserController@index');
$router->get('/user/{id}','UserController@index')->name('user.index');
$router->post('/create/user','UserController@store')->name('user.store');
$router->get('/delete/user/{id}','UserController@delete')->name('user.delete');
$router->post('/update/user/{id}','UserController@update')->name('user.update');

$router->get('/group','GroupController@index');
$router->get('/group/{id}','GroupController@index')->name('group.index');
$router->post('/create/group','GroupController@store')->name('group.store');
$router->get('/delete/group/{id}','GroupController@delete')->name('group.delete');
$router->post('/update/group/{id}','GroupController@update')->name('group.update');

$router->controller();

?>