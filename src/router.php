<?php

require "libs/config.php";
require "libs/router.php";
require "libs/database.php";
require "src/config.php";

$router=new Router();

$router->get('/prueba','Prueba@metodo');
$router->get('/',function($request){
	view('raau',$request);
});
$router->error(404,function($request){
	view('json');
});
$router->get("/test",function(){

});

$router->controller();

?>