<?php

use libs\Router;
use libs\Config;

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
    echo config('APP_URL')."/".$text;
}

function urlPublic($text){
    echo config('APP_PUBLIC')."/".$text;
}

function redirect($url){
    return header("location:".config('APP_URL')."/".$url);
}

?>