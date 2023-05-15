<?php

use libs\Router\Router;
use libs\Config;
use libs\Model;

function dd($obj){
    echo "<pre>".print_r($obj,"<br>")."</pre>";
}

function abort($code){
    http_response_code($code);
    exit;
}

function route($name,...$params){
    $router=Router::singletong();
    $url="";
    foreach($router->getRoutes() as $route){
        if($route->name!=$name){
            continue;
        }
        $url="";
        foreach(explode("/",$route->path) as $key=>$path){
            if(preg_match('/{.*?}/',$path,$param)){
                $url.="/".array_shift($params);
            }else{
                $url.="/".$path;
            }
        }
    }
    if($url==""){
        throw new Exception("Route (".$name.") not found");
    }
    return url($url);
}

function view($path,$params=[]){
    Router::view($path,$params);
}

function json($array){
    header("Content-type: application/json");
    return json_encode($array);
}

function config($key){
    return Config::singleton()->get($key);
}

function url($text){
    //\dirname($_SERVER['PHP_SELF'])
    $text=trim($text,"/");
    return str_replace("\\","/",(isset($_SERVER['HTTPS'])?"https":"http")."://".$_SERVER["HTTP_HOST"])."/".$text;
}

function urlPublic($text){
    return url(config('APP_PUBLIC')."/".$text);
}

function redirect($url=null){
    if($url==null){
        $url=url("");
    }
    header("location:".$url);
    exit;
}

function back(){
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}

?>