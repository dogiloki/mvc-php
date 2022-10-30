<?php

require "libs/config.php";
require "libs/secure.php";
require "libs/router.php";
require "libs/database.php";
require "src/config.php";

$router=new Router();
$router->get('/prueba','ControllerPrueba@metodo');
$router->get('/',function(){
	view('rauu');
});

$router->controller();

?>