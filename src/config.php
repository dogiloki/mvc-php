<?php

use libs\Config;

$config=Config::singleton();

// Base de datos
if(true){
	$config->set('host','localhost');
	$config->set('user','root');
	$config->set('password','');
	$config->set('db','');
}else{
	$config->set('host','');
	$config->set('user','');
	$config->set('password','');
	$config->set('db','');
}

// Encabezado
$config->set('key','soy_un_key');

// Directorios
$config->set('url',str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"]).dirname($_SERVER['PHP_SELF'])."/");
$config->set('public',$config->get('url')."public/");

?>