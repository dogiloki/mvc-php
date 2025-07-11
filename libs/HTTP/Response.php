<?php

namespace libs\HTTP;

use libs\View\View;
use libs\Router\Router;

class Response{

    public function __construct($code=200){
        http_response_code($code);
    }

    public function message($message){
        echo $message;
        exit;
    }

    public function json($array,$code=null){
        header("Content-type: application/json");
        if($code!=null){
            http_response_code($code);
        }
        return json_encode($array);
    }

    public function view($path,$params=[]){
        return (new View())->make($path,$params);
    }

    public function file($path,$params=[]){
        if(file_exists($path)){
            readfile($path);
            exit;
        }else{
            abort(500);
        }
    }

    public function component($name,$params=[]){
        return (new View())->component($name,$params);
    }

    public function abort($code,$message=null){
        if($code!=null){
            http_response_code($code);
        }
        if($message==null){
            $message=match($code){
                404=>"Not found",
                403=>"Forbidden",
                401=>"Unauthorized",
                500=>"Internal Server Error",
                400=>"Bad Request",
                419=>"Page Expired",
                405=>"Method Not Allowed",
                408=>"Request Timeout",
                429=>"Too Many Requests",
                503=>"Service Unavailable",
                504=>"Gateway Timeout",
                default=>"Error"
            };
        }
        echo "<h1>".$code." - ".$message."</h1>";
        exit;
    }

    public function route($name,...$params){
        $router=Router::singleton();
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
            throw new \Exception("Route (".$name.") not found");
        }
        return url($url);
    }

    public function redirect($url=null){
        if($url==null){
            $url=url("");
        }
        header("location:".$url);
        exit;
    }

    public function back(){
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit;
    }

    public function reload(){
        header("Location: ".$_SERVER['REQUEST_URI']);
        exit;
    }

    public function exception($ex){
        if(config()->app('debug')){
            abort(500,
                "<br>Message: ".$ex->getMessage()."<br>".
                "File: ".$ex->getFile()."<br>".
                "Line: ".$ex->getLine()
            );
        }else{
            abort(500);
        }
    }

    public function error(...$params){
        foreach($params as $key=>$param){
            echo $param."\n";
        }
    }

}

?>