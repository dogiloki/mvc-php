<?php

namespace libs\Router;

use libs\Middle\Middle;

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
    public $middleware;

    public function __construct(string $method, string $path, $action){
        $this->method=$method;
        $this->path=Route::formatUri($path);
        $this->action=$action;
        $this->params=[];
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

    public function call($params=[]){
        $middleware=$this->middleware;
        $action=$this->action;
        if($middleware!=null){
            if(is_string($this->middleware)){
                $middleware=new (Middle::config('middle','auth'))();
            }else{
                $middleware=new ($this->middleware)();
            }
            echo $middleware->redirectTo(function()use($action,$params){
                if($action instanceof \Closure){
                    return $action($params);
                }else{
                    $controller=explode('@',$action);
                    $controller[0]="controllers\\".$controller[0];
                    $obj=new $controller[0];
                    return $obj->{$controller[1]}($params);
                }
            });
        }else{
            if($action instanceof \Closure){
                echo $action($params);
            }else{
                $controller=explode('@',$action);
                $controller[0]="controllers\\".$controller[0];
                $obj=new $controller[0];
                echo $obj->{$controller[1]}($params);
            }
        }
    }

}

?>