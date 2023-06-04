<?php

function url($text=""){
    //\dirname($_SERVER['PHP_SELF'])
    $text=trim($text,"/");
    return str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".($_SERVER["HTTP_HOST"]??''))."/".$text;
}

require_once "libs/Config.php";
use libs\Config;

spl_autoload_register(function($clase){
	$path=str_replace("\\","/",$clase).".php";
	//echo $path."<br>";
	$cache=Config::filesystem('cache');
	$cache_folder=$cache['path'];
	$cache_file=$cache_folder."/".$cache['file'];
	$json=(array)json_decode(file_get_contents($cache_file));
	$slug=slug($path);
	if(!isset($json[$slug])){
		$path=explode("/",$path);
		$json=array_merge($json,listDirectory(join("/",array_slice($path,0,count($path)-1))));
		file_put_contents($cache_file,json_encode($json));
	}
	if(file_exists($json[$slug])){
		require_once $json[$slug];
	}
});

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

function slug($text){
	return strtolower(preg_replace("/[^a-zA-Z0-9]+/","-",$text));
}

require "vendor/autoload.php";
require_once "libs/helpers.php";
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
// Enrutadores
use libs\Router\Router;
$directory=scandir(Config::filesystem("routes.path"));
$router=Router::singletong();
// Rutas predeterminadas
$router->get('/storage1/{disk}',function($request){
	return \libs\Middle\Storage::get($request->input('file'),$request->input('disk'));
});
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