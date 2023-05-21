<?php

namespace libs\Router;

use libs\Config;
use libs\HTTP\Request;

class Route{

    public static function formatUri(string $uri){
        $uri=trim(preg_replace('#(/)+#','/',$uri),"/");
        return $uri;
    }

    public $name_file;
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

    public function call(Request $request, $index_middlewares=0){
        $middleware=$this->middlewares[$index_middlewares]??null;
        if($middleware==null){
            $this->callAction($request);
            exit;
        }
        if(is_object($middleware)){
            $middleware=new $middleware();
        }else{
            $middleware=new (Config::middleware("alias.".$middleware))();
        }
        try{
            $middleware->handle($request,function($request)use($index_middlewares){
                $this->call($request,$index_middlewares+1);
            });
            $middleware->terminate($request,function($request)use($index_middlewares){
                $this->call($request,$index_middlewares+1);
            });
        }catch(\Exception $ex){
            $middleware->report($ex);
        }
    }

    private function callAction(Request $request){
        $action=$this->action;
        if($action instanceof \Closure){
            echo $action($request);
        }else{
            $controller=explode('@',$action);
            $controller[0]=Config::filesystem('controllers')."\\".$controller[0];
            $obj=new $controller[0];
            echo $obj->{$controller[1]}($request);
        }
    }

}

?>