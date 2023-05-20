<?php

use libs\Router\Router;
use libs\HTTP\Response;
use libs\Auth\Auth;
use libs\Env;
use libs\Config;
use libs\Middle\Session;

Env::singleton("config.env");

function dd($obj){
    echo "<pre>".print_r($obj,"<br>")."</pre>";
}

function env($key){
    return Env::get($key);
}

function csrfToken(){
    return Session::csrfToken();
}

function auth(){
    return Auth::check();
}

function user(){
    return Auth::user();
}

function url($text=""){
    //\dirname($_SERVER['PHP_SELF'])
    $text=trim($text,"/");
    return str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".($_SERVER["HTTP_HOST"]??''))."/".$text;
}

function urlPublic($text){
    return Config::filesystem('public.url')."/".$text;
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