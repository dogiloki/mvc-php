<?php

spl_autoload_register(function($clase){
	$path=str_replace("\\","/",$clase).".php";
	//echo $path."<br>";
	if(file_exists($path)){
		require_once($path);
	}
});

// Configuración
use libs\Config;
$cfg=Config::singleton("src/config.cfg");
//$cfg->set('APP_URL',str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"]).\dirname($_SERVER['PHP_SELF'])."/");

require_once("src/router.php");

?>