<?php

namespace libs\HTTP;

use libs\Router\Router;

class Response{

    public static function json($array){
        header("Content-type: application/json");
        return json_encode($array);
    }

    public static function view($path,$params=[]){
        Router::view($path,$params);
    }

    public static function abort($code){
        http_response_code($code);
        exit;
    }

    public static function route($name,...$params){
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

    public static function redirect($url=null){
        if($url==null){
            $url=url("");
        }
        header("location:".$url);
        exit;
    }

    public static function back(){
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }

}

?>