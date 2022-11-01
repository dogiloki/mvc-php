<?php

require "libs/config.php";
require "libs/secure.php";
require "libs/router.php";
require "libs/database.php";
require "src/config.php";

$router=new Router();
$router->get('/prueba','ControllerPrueba@metodo');
$router->get('/',function($request){
	view('rauu',$request);
});
$router->error(404,function(){
	view('json');
});

$router->controller();

?>