<?php

namespace libs\View;

use libs\HTTP\Request;
use libs\Router\Route;

abstract class Component{

    protected $render="";
    protected $params=[];
    protected $middleware=[];

    public function __construct(){
        $this->getProperties();
    }

    public function getProperties(){
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->name;
            $this->params[$name]=$this->$name;
        }
        return $this->params;
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


    public function render(){
        return view($this->render,$this->getData());
    }

}

?>