<?php

$config=Config::singleton();

// Base de datos
$config->set('host','localhost');
$config->set('user','root');
$config->set('password','');
$config->set('db','');

// Encabezado
$config->set('key','75e36d4ef6f1172c8a2c61a8792e57e957dee9a7d03db1ebe750c3a0bcd650ee');

// Directorios
$config->set('url',str_replace("\\","/","http://".$_SERVER["HTTP_HOST"]).dirname($_SERVER['PHP_SELF'])."/");
$config->set('public',$config->get('url')."public/");

?>