<?php

use libs\Router;
use libs\Config;

function route($name){
    $router=Router::singletong();
    url(trim($router->getUrls()[$name]['url'],"/"));
}

function view($path,$params=[]){
    Router::view($path,$params);
}

function json($text){
    header("Content-type: application/json");
    return json_encode($text);
}

function config($key){
    return Config::singleton()->get($key);
}

function url($text){
    //\dirname($_SERVER['PHP_SELF'])
    echo str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"])."/".$text;
}

function urlPublic($text){
    echo url(config('APP_PUBLIC')."/".$text);
}

function redirect($url){
    return header("location:".config('APP_URL')."/".$url);
}

?>