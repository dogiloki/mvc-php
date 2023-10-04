<?php

namespace libs\View;

use libs\HTTP\Request;
use libs\Router\Route;

abstract class Component{

    protected $render="";
    protected $params=[];
    protected $middleware=[];

    public function init(Request $request){
        $route=new Route();
        $route->middlewares=$this->middleware;
        if(!$route->call($request,0,false)){
            return false;
        }
        $params=$request->post;
        $json=json_decode($params['json']??[]);
        $this->params=(array)($json->params??[]);
        foreach($this->params as $name=>$value){
            $this->$name=$value;
            $this->updating($name,$value);
        }
        $method=(array)($json->method??[]);
        if(isset($method['name'])){
            $this->{$method['name']}(...($method['params']??[]));
        }
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->name;
            $value=$params[$name]??$this->$name;
            $this->params[$name]=$value;
        }
        return true;
    }

    public function syncInput($name,$filter=null){
        $value=$this->params[$name]??$this->$name;
        if($filter!=null){
            $value=filter_var($value,$filter);
        }
        $this->$name=$value;
        return $value;
    }

    public function getParams(){
        return $this->params;
    }

    public function direct(){
        return url();
    }

    public function updating($name,$value){

    }


    public function render(){
        return view($this->render,$this->params);
    }

}

?>