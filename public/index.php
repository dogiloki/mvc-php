<?php

chdir("../");

require_once "vendor/autoload.php";
require_once "libs/helpers.php";

use libs\Config;

function listDirectory($path){
	if(!is_dir($path) || !file_exists($path) || !is_readable($path) || $path==""){
		return [];
	}
	$json=[];
	foreach(scandir($path) as $file){
		if($file=="." || $file==".." || $file=="vendor" || $file==".git"){
			continue;
		}
		$file_path=$path."/".$file;
		if(is_dir($file_path)){
			continue;
		}
		$json[slug($file_path)]=$file_path;
	}
	return $json;
}

//$env->set('APP_URL',str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"]).\dirname($_SERVER['PHP_SELF'])."/");
// Verificar archivos publicos
$uri=$_SERVER['REQUEST_URI'];
if(file_exists(Config::filesystem("public.path").$uri) && !is_dir(Config::filesystem("public.path").$uri)){
	$ext=explode(".",$uri)[1];
	$mime=match($ext){
		"css"=>"text/css",
		"js"=>"text/javascript",
		"png"=>"image/png",
		"jpg"=>"image/jpg",
		"jpeg"=>"image/jpeg",
		"gif"=>"image/gif",
		"svg"=>"image/svg+xml",
		default=>"text/plain"
	};
	header("Content-Type: ".$mime);
	echo file_get_contents(Config::filesystem("public.path").$uri);
	exit;
}
unset($uri);

// Zona horaria
date_default_timezone_set(env('TIMEZONE',Config::app('timezone')??date_default_timezone_get()));

// Llamdo del Kernel
use app\Kernel;
$kernel=new Kernel();

// Llamado del enrutamiento
use libs\Router\Route;
Route::controller();


?>