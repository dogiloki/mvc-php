<?php

use libs\HTTP\Response;
use libs\Auth\Auth;
use libs\Session\Session;
use libs\Env;
use libs\Config;
use ScssPhp\ScssPhp\Compiler;

Env::singleton("config.env");

env('APP_BASE_PATH',dirname(__DIR__));

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

function base_path($path=""){
    return env('APP_BASE_PATH')."\\".$path;
}

function public_path($path=""){
    return env('APP_BASE_PATH')."\\public\\".$path;
}

function url($text=""){
    //\dirname($_SERVER['PHP_SELF'])
    $text=trim($text,"/");
    $is_https=(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']==='on') ||
           (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']==='https');
    return str_replace("\\", "/", ($is_https?"https":"http")."://".($_SERVER["HTTP_HOST"]??''))."/".$text;
    //return str_replace("\\","/",env('APP_HOST')."/".$text);
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
    $path=messageFormat(Config::filesystem('scss.file_output'),[
        'name'=>str_replace(".scss",".css",$path)
    ]);
    return url($path);
}

function style($path){
    return "<link rel=\"stylesheet\" href=\"".url("css/".$path)."\">";
}

function script($path){
    return "<script type=\"module\" src=\"".url("js/".$path)."\"><script/>";
}

function locale($locale=null){
    return $locale==null?Config::app('locale')??Session::get('locale'):Session::set('locale',$locale);
}

function __($key,$params=[]){
    $keys=explode(".",$key);
    $locale=locale();
    $file=Config::filesystem('lang.path')."/".$locale."/".$keys[0].".php";
    if(file_exists($file)){
        $lang=include($file);
        $value=$lang;
        foreach($keys as $index=>$key){
            if($index==0){
                continue;
            }
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
    return $keys[count($keys)-1]??null;
}

function messageFormat($text,$args=[],$separator="{}"){
    if($separator==="{}"){
        return preg_replace_callback('/\{(\w+)\}/',function($matches)use($args){
            $name=$matches[1];
            return isset($args[$name])?$args[$name]:$matches[0];
        },$text);
    }else
    if($separator===":"){
        foreach($args as $key=>$arg){
            $text=str_replace(":".$key,$arg,$text);
        }
        return $text;
    }
}

// Functions from Response::class

function response($code=200): Response{
    return new Response($code);
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
function error(...$params){
    return response()->error(...$params);
}

?>