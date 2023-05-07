<?php

use libs\Router;
use libs\DB;
use libs\Session;
use models\User;
use libs\Secure;

$router=new Router();

$router->get('/prueba','PruebaController@metodo');
$router->get('/',function($request){
	view('index',$request);
});
$router->get("/test",function($request){
	try{
		$user=User::find(1);
		print_r($user);
	}catch(\Exception $ex){
		echo $ex->getMessage();
	}
});

$router->controller();

?>