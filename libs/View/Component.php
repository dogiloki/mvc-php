<?php

namespace libs\View;

use libs\HTTP\Request;
use libs\Router\Route;

abstract class Component{

    protected $render="";
    protected $middleware=[];

    public function __construct(){
        $this->getProperties();
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


    public function render(){
        return view($this->render,$this->getData());
    }

}

?>