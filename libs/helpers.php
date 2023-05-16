<?php

use libs\Router\Router;
use libs\HTTP\Response;
use libs\Auth\Auth;
use libs\Config;
use libs\Model;

function dd($obj){
    echo "<pre>".print_r($obj,"<br>")."</pre>";
}

function config($key){
    return Config::get($key);
}

function auth(){
    return Auth::user();
}

function url($text){
    //\dirname($_SERVER['PHP_SELF'])
    $text=trim($text,"/");
    return str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"])."/".$text;
}

function urlPublic($text){
    return url(config('APP_PUBLIC')."/".$text);
}

// Functions from Response::class

function response(){
    return Response::class;
}
function json(...$params){
    return Response::json(...$params);
}
function view(...$params){
    return Response::view(...$params);
}
function abort(...$params){
    return Response::abort(...$params);
}
function route(...$params){
    return Response::route(...$params);
}
function redirect(...$params){
    return Response::redirect(...$params);
}
function back(...$params){
    return Response::back(...$params);
}

?>