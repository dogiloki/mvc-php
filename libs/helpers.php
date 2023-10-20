<?php

use libs\HTTP\Response;
use libs\Auth\Auth;
use libs\Session\Session;
use libs\Env;
use libs\Config;
use ScssPhp\ScssPhp\Compiler;

Env::singleton("config.env");

function config(): Config{
    return new Config();
}

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

function url($text=""){
    //\dirname($_SERVER['PHP_SELF'])
    $text=trim($text,"/");
    return str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".($_SERVER["HTTP_HOST"]??''))."/".$text;
}

function slug($text){
	return strtolower(preg_replace("/[^a-zA-Z0-9]+/","-",$text));
}

function vars(){
    return get_defined_vars();
}

function env($key,$default=null){
    if(!Env::has($key)){
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

function session(){
    return Session::singleton();
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
        }catch(Exception $ex){
            exception($ex);
        }
    }
    $path=Config::filesystem('scss.path_public')."/".str_replace(".scss",".css",$path);
    return url($path);
}

function locale($locale=null){
    return $locale==null?Config::app('locale')??Session::get('locale'):Session::set('locale',$locale);
}

function __($key, $params=[]){
    $locale=locale();
    $file=Config::filesystem('lang.path')."/".$locale.".php";
    if(file_exists($file)){
        $lang=include($file);
        $keys=explode(".",$key);
        $value=$lang;
        foreach($keys as $key){
            $value=$value[$key]??$key;
        }
        if(is_array($value)){
            $value=$key;
        }
        if(is_string($value)){
            foreach($params as $key=>$param){
                $value=str_replace(":".$key,$param,$value);
            }
        }
        return $value;
    }
    return null;
}

function messageFormat($text,$args=[]){
    return preg_replace_callback('/\{(\w+)\}/',function($matches)use($args){
        $name=$matches[1];
        return isset($args[$name])?$args[$name]:$matches[0];
    },$text);
}

// Functions from Response::class

function response(): Response{
    return new Response();
}
function json(...$params){
    return response()->json(...$params);
}
function view(...$params){
    return response()->view(...$params);
}
function component(...$params){
    return response()->component(...$params);
}
function abort(...$params){
    return response()->abort(...$params);
}
function route(...$params){
    return response()->route(...$params);
}
function redirect(...$params){
    return response()->redirect(...$params);
}
function back(...$params){
    return response()->back(...$params);
}
function reload(...$params){
    return response()->reload(...$params);
}
function exception(...$params){
    return response()->exception(...$params);
}

?>