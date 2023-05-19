<?php

spl_autoload_register(function($clase){
	$path=str_replace("\\","/",$clase).".php";
	//echo $path."<br>";
	if(file_exists($path)){
		require_once($path);
	}
});

require "vendor/autoload.php";
require_once "libs/helpers.php";
//$env->set('APP_URL',str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"]).\dirname($_SERVER['PHP_SELF'])."/");
// Enrutadores
use libs\Router\Router;
use libs\Config;
$directory=scandir(Config::filesystem("routes.path"));
$router=Router::singletong();
foreach($directory as $file){
	if($file=='.' || $file=='..'){
		continue;
	}
	$ext=explode(".",$file)[1];
	if($ext!="php"){
		continue;
	}
	require_once(Config::filesystem("routes.path")."/".$file);
}
$router->controller();

?>