<?php

use libs\HTTP\Response;
use libs\Auth\Auth;
use libs\Session\Session;
use libs\Env;
use libs\Config;

Env::singleton("config.env");

function dd($obj){
    $trace=debug_backtrace();
    $file=$trace[0]['file'];
    $line=$trace[0]['line'];
    echo $file;
    echo "<br>";
    echo $line;
    echo "<br>";
    echo "<pre>".print_r($obj,"<br>")."</pre>";
}

function folderRoot($path=""){
    $folder=explode("/",explode(".",$_SERVER['PHP_SELF'])[0]??"/");
    return join("/",array_slice($folder,0,count($folder)-1))."/".$path;
}

function env($key,$default=null){
    $value=Env::get($key);
    if($value==null){
        Env::set($key,$default);
    }
    return Env::get($key);
}

function csrfToken(){
    return Session::token();
}

function auth(){
    return Auth::check();
}

function user(){
    return Auth::user();
}

function urlPublic($text){
    return Config::filesystem('public.url')."/".$text;
}

function storagePath($text=""){
    return Config::filesystem('storage.path')."/".$text;
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
function component(...$params){
    return Response::component(...$params);
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
function reload(...$params){
    return Response::reload(...$params);
}

?>