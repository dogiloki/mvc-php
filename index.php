<?php

spl_autoload_register(function($clase){
	$path=str_replace("\\","/",$clase).".php";
	//echo $path."<br>";
	if(file_exists($path)){
		require_once($path);
	}
});

require_once "libs/helpers.php";
//$env->set('APP_URL',str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"]).\dirname($_SERVER['PHP_SELF'])."/");
// Enrutadores
$directory=scandir("routers");
use libs\Router\Router;
$router=Router::singletong();
foreach($directory as $file){
	if($file=='.' || $file=='..'){
		continue;
	}
	$ext=explode(".",$file)[1];
	if($ext!="php"){
		continue;
	}
	require_once("routers/".$file);
}
$router->controller();

?>