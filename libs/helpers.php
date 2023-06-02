<?php

use libs\HTTP\Response;
use libs\Auth\Auth;
use libs\Session\Session;
use libs\Env;
use libs\Config;
use Leafo\ScssPhp\Compiler;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

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

function storagePath($text=""){
    return Config::filesystem('storage.path')."/".$text;
}

function scss($path){
    $path_input=Config::filesystem('scss.path_input');
    $path_output=Config::filesystem('scss.path_output');
    $file_input=$path_input."/".$path;
    $file_output=$path_output."/".explode(".",$path)[0].".css";
    if(file_exists($file_output)==false || filemtime($file_input)>filemtime($file_output)){
        try{
            $scss=new Compiler();
            $scss->setImportPaths($path_input);
            $css=$scss->compile(file_get_contents($file_input));
            $path_output=dirname($file_output);
            if(!is_dir($path_output)){
                mkdir($path_output,0777,true);
            }
            file_put_contents($file_output,$css);
        }catch(Exception $e){
            
        }
    }
    $path=Config::filesystem('scss.path_public')."/".str_replace(".scss",".css",$path);
    return url($path);
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