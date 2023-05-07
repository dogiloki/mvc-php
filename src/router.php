<?php

use libs\Router;

$router=Router::singletong();

$router->get('/{id}','UserController@index');
$router->post('/user','UserController@store')->name('user.store');

$router->controller();

?>