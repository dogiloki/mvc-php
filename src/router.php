<?php

use libs\Router;
use libs\DB;

$router=new Router();

$router->get('/prueba','Prueba@metodo');
$router->get('/',function($request){
	view('index',$request);
});
$router->error(404,function($request){
	view('json');
});
$router->get("/test",function(){

});

$router->controller();

?>