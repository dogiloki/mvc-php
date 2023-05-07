<?php

use libs\Router;

$router=Router::singletong();

$router->get('/','UserController@index');
$router->post('/user','UserController@store')->name('user.store');

$router->controller();

?>