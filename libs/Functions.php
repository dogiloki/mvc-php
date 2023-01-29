<?php

use libs\Cofing;
use libs\Router;

function view($path,$params=[]){
	Router::view($path,$params);
}
function config($key){
	echo libs\Config::singleton()->get($key);
}

?>