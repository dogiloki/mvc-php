<?php

use libs\Router;
use libs\Config;

function route($name,$params=[]){
    $router=Router::singletong();
    $url=url(trim(($router->getUrls()[$name]??"")['url']??"","/"));
    $url.="/".implode("/",$params);
    return $url;
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
    return str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"])."/".$text;
}

function urlPublic($text){
    return url(config('APP_PUBLIC')."/".$text);
}

function redirect($url){
    return header("location:".$url);
}

function back(){
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}

?>