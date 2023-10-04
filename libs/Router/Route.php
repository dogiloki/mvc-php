<?php

namespace libs\Router;

use libs\Config;
use libs\HTTP\Request;

class Route{

    public static function formatUri(string $uri){
        $uri=explode("/?",$uri)[0];
        $uri=trim(preg_replace('#(/)+#','/',$uri),"/");
        return $uri;
    }

    public $name_file;
    public $method;
    public $path;
    public $action;
    public $params;
    public $name;
    public $middlewares=[];

    public function __construct(string $method=null, string $path=null, $action=null){
        $this->method=$method;
        $path=folderRoot($path);
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

    public function call(Request $request, $index_middlewares=0, $do_call=true){
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
            $middleware=new (Config::middleware("alias.".$middleware))();
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