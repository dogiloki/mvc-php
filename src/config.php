<?php

use libs\Config;

$config=Config::singleton();

// Base de datos
if(true){
	$config->set('db_host','localhost');
	$config->set('db_user','root');
	$config->set('db_password','');
	$config->set('db_name','');
}else{
	$config->set('db_host','');
	$config->set('db_user','');
	$config->set('db_password','');
	$config->set('db_name','');
}

// Encabezado
$config->set('key','soy_un_key');

// Directorios
$config->set('url',str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"]).dirname($_SERVER['PHP_SELF'])."/");
$config->set('public',$config->get('url')."public/");

?>