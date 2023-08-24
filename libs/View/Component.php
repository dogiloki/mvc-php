<?php

namespace libs\View;

use libs\HTTP\Request;

abstract class Component{

    protected $render="";
    protected $params;

    public function __construct(){

    }

    public function mount(){

    }

    public function syncInput($params){
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->getName();
            $this->$name=$params[$name]??$this->$name;
        }
        $this->mount();
    }


    public function render(){
        return view($this->render,get_object_vars($this));
    }

}

?>