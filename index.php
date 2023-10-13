<?php

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
	if(file_exists($json[$slug]??"")){
		require_once $json[$slug];
	}
});

require "vendor/autoload.php";
require_once "libs/helpers.php";

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

// Activar mostrar errores
ini_set('display_errors',env('APP_DEPLOY')?1:0);
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
use app\Kernel;
$directory=scandir(Config::filesystem("routes.path"));
$router=Router::singleton();
$kernel=new Kernel();

// Zona horaria
date_default_timezone_set(env('TIMEZONE',Config::app('timezone')??date_default_timezone_get()));

$router->post('/component/{name}',function($request){
	ob_start();
	$name=ucfirst($request->input('name'));
	$instance=new ("\\".str_replace("/","\\",Config::filesystem("components.path"))."\\".$name)();
	$done=$instance->syncRequest($request);
	$instance->render();
	$html=ob_get_clean();
	$data=$instance->getData();
	return json([
		"html"=>$done?$html:"",
		"data"=>$data,
		"direct"=>$done?null:$instance->direct()
	]);
})->middleware('session','csrf');
/* Rutas predeterminadas
$router->get('/storage1/{disk}',function($request){
	return \libs\Middle\Storage::get($request->input('file'),$request->input('disk'));
});
*/
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