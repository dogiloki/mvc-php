<?php

namespace libs\View;

use libs\HTTP\Request;

abstract class Component{

    protected $render="";
    protected $params=[];

    public function init($params=[]){
        $json=json_decode($params['json']??[]);
        $this->params=(array)$json->params??[];
        foreach($this->params as $name=>$value){
            $this->$name=$value;
            $this->updating($name,$value);
        }
        $reflection=new \ReflectionClass($this);
        $properties=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $name=$property->name;
            $value=$params[$name]??$this->$name;
            $this->params[$name]=$value;
        }
    }

    public function syncInput($name,$filter=null){
        $value=$this->params[$name]??$this->$name;
        if($filter!=null){
            $value=filter_var($value,$filter);
        }
        $this->$name=$value;
        return $value;
    }

    public function updating($name,$value){

    }


    public function render(){
        return view($this->render,$this->params);
    }

}

?>