<?php

namespace libs\Router;

use libs\Config;
use libs\HTTP\Request;
use libs\Router\Router;
use libs\Middleware\Middleware;

class Route{

    public static function __callStatic($method,$arguments){
        $instance=Router::singleton();
        if(method_exists($instance,$method)){
            return call_user_func_array([$instance,$method],$arguments);
        }
    }

    public static function formatUri($uri){
        $uri=explode("/?",$uri)[0];
        $uri=trim(preg_replace('#(/)+#','/',$uri),"/");
        return $uri;
    }

    public $method;
    public $path;
    public $action;
    public $params;
    public $name;
    public $middlewares=[];
    public $group;

    public function __construct($method=null,$path=null,$action=null){
        $this->method=$method;
        $this->path=Route::formatUri($path??"");
        $this->action=$action;
        $this->params=[];
        $this->name=null;
        $this->middlewares=[];
        $this->group=null;
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

    public function call(Request $request,$index_middlewares=0,$do_call=true){
        $middleware=$this->middlewares[$index_middlewares]??null;
        if($middleware==null){
            if($do_call==1){
                $this->callAction($request);
                exit;   
            }
            return true;
        }
        if(class_exists($middleware)){
            $middleware=new $middleware();
        }else{
            $middleware=new (Middleware::alias($middleware))();
        }
        if($middleware===null){
            return $this->call($request,$index_middlewares+1,$do_call);
        }
        try{
            return $middleware->handle($request,function($request)use($index_middlewares,$do_call){
                return $this->call($request,$index_middlewares+1,$do_call);
            });
            return $middleware->terminate($request,function($request)use($index_middlewares,$do_call){
                return $this->call($request,$index_middlewares+1,$do_call);
            });
        }catch(\Exception $ex){
            return $middleware->report($ex);
        }
        return false;
    }
    
    private function callAction(Request $request){
        $action=$this->action;
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-Token, X-XSRF-TOKEN");
        header("Access-Control-Allow-Credentials: true");
        if($action instanceof \Closure){
            echo $action($request);
        }else{
            $controller=explode('@',$action);
            $controller[0]=str_replace("/","\\",Config::filesystem('controllers.path'))."\\".$controller[0];
            $obj=new $controller[0];
            echo $obj->{$controller[1]}($request);
        }
    }

}

?>