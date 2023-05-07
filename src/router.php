<?php

use libs\Router;

$router=new Router();

$router->get('/','UserController@index');
$router->post('/user','UserController@store');

$router->controller();

?>