<?php

use libs\Router;

$router=Router::singletong();

$router->get('/{id}','UserController@index');
$router->post('/create/user','UserController@store')->name('user.store');
$router->get('/delete/user/{id}','UserController@delete')->name('user.delete');

$router->controller();

?>