<?php

namespace libs\View;

use libs\HTTP\Request;
use libs\Router\Route;
use libs\Middleware\Middleware;

abstract class Component{

    protected $model=null;
    protected $view="";
    protected $middlewares=[];

    public function __construct(){
        $this->getProperties();
    }

    public function call(Request $request,$index_middlewares=0){
        $middleware=$this->middlewares[$index_middlewares]??null;
        if($middleware==null){
            return true;
        }
        if(class_exists($middleware)){
            $middleware=new $middleware();
        }else{
            $middleware=new (Middleware::alias($middleware))();
        }
        if($middleware===null){
            return $this->call($request,$index_middlewares+1);
        }
        try{
            return $middleware->handle($request,function($request)use($index_middlewares){
                return $this->call($request,$index_middlewares+1);
            });
            return $middleware->terminate($request,function($request)use($index_middlewares){
                return $this->call($request,$index_middlewares+1);
            });
        }catch(\Exception $ex){
            return $middleware->report($ex);
        }
        return false;
    }

    public function getProperties(){
        $params=[];
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->name;
            $params[$name]=$this->$name;
        }
        return $params;
    }

    public function setProperties($values){
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->name;
            if(property_exists($values,$name)){
                $this->{$name}=$values->{$name};
            }
        }
    }

    public function direct(){
        return url();
    }

    public function updating($name,$value){

    }

    public function view(){
        return view($this->view,$this->getProperties());
    }

}

?>