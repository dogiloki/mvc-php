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
	view('index',$request);
});
$router->get("/test",function($request){
	try{
		$group=new Group();
		$group->name="3623IS";
		$group->description="Grupo de administradores";
		$group->save();
		$user=new User();
		$user->name="Julio";
		$user->email="villanueva";
		$user->password=Secure::encodePassword("123");
		$user->group=$group;
		$user->save();
	}catch(\Exception $ex){
		echo $ex->getMessage();
	}
});

$router->controller();

?>