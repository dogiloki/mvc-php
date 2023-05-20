<?php

namespace libs\Router;

use libs\Config;

class Route{

    public static function formatUri(string $uri){
        $uri=trim(preg_replace('#(/)+#','/',$uri),"/");
        return $uri;
    }

    public $method;
    public $path;
    public $action;
    public $params;
    public $name;
    public $middlewares;

    public function __construct(string $method, string $path, $action){
        $this->method=$method;
        $this->path=Route::formatUri($path);
        $this->action=$action;
        $this->params=[];
        $this->middlewares=[];
        $this->generateParams();
    }

    private function generateParams(){
        $path=explode("/",$this->path);
        foreach($path as $key=>$value){
            if(preg_match('/{.*?}/',$value,$param)){
                $name=str_replace(["{","}","?"],"",$param[0]);
                $this->params[$key]=[
                    'name'=>$name,
                    'optional'=>substr($param[0],-2,1)=="?"?true:false
                ];
            }
        }
    }

    public function call($params){
        $middlewares=$this->middlewares;
        $action=$this->action;
        foreach($middlewares as $middleware){
            if(is_string($middleware)){
                $name=Config::middleware('alias.'.$middleware);
                $middleware=new $name();
            }else{
                $middleware=new $middleware();
            }
            if(!$middleware->handle()){
                $middleware->redirectTo();
            }
            $middleware->terminate();
        }
        if($action instanceof \Closure){
            echo $action($params);
        }else{
            $controller=explode('@',$action);
            $controller[0]=Config::filesystem('controllers')."\\".$controller[0];
            $obj=new $controller[0];
            echo $obj->{$controller[1]}($params);
        }
    }

}

?>