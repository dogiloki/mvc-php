<?php

namespace libs\View;

use libs\HTTP\Request;
use libs\Router\Route;

abstract class Component{

    protected $render="";
    protected $params=[];
    protected $middleware=[];

    public function __construct(){
        $this->getData();
    }

    public function syncRequest(Request $request){
        $route=new Route();
        $route->middlewares=$this->middleware;
        if(!$route->call($request,0,false)){
            return false;
        }
        $this->params=$request->data??[];
        $index=0;
        while($index<count($this->params)){
            $name=key($this->params);
            $value=current($this->params);
            $this->$name=$value;
            $this->updating($name,$value);
            next($this->params);
            $index++;
        }
        $method=$request->method??null;
        if($method!=null && isset($method['name'])){
            $this->{$method['name']}(...($method['params']??[]));
        }
        return true;
    }

    public function sync($name,$value=null){
        if(func_num_args()==1){
            $value=$this->params[$name]??$this->$name;
        }
        $this->params[$name]=$value;
        $this->$name=$value;
        return $value;
    }

    public function getData(){
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->name;
            $this->params[$name]=$this->$name;
        }
        return $this->params;
    }

    public function direct(){
        return url();
    }

    public function updating($name,$value){

    }


    public function render(){
        return view($this->render,$this->getData());
    }

}

?>