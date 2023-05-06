<?php

use libs\Router;
use libs\DB;

$router=new Router();

$router->get('/prueba','PruebaController@metodo');
$router->get('/',function($request){
	view('index',$request);
});
$router->get("/test",function($request){

});

$router->controller();

?>