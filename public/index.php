<?php

chdir("../");

// Llamado de autoload
require_once "vendor/autoload.php";

// Lamado de helpers
require_once "libs/helpers.php";

// Zona horaria
use libs\Config;
date_default_timezone_set(env('TIMEZONE',Config::app('timezone')??date_default_timezone_get()));

// Llamdo del Kernel
use app\Kernel;
$kernel=new Kernel();

// Llamado del enrutamiento
use libs\Router\Route;
Route::controller();

?>