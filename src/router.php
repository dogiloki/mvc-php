<?php

use libs\Router;
use libs\DB;
use libs\Session;
use models\User;
use models\Group;
use libs\Secure;

$router=new Router();

$router->get('/prueba','PruebaController@metodo');
$router->get('/',function($request){
	$user=User::find(1);
	view('user.index',compact('user'));
});
$router->get("/test",function($request){
	try{
		echo json_encode();
	}catch(\Exception $ex){
		echo $ex->getMessage();
	}
});

$router->controller();

?>