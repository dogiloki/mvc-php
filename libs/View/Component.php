<?php

namespace libs\View;

use libs\Http\Request;
use libs\Middle\Log;

abstract class Component{

    protected $_attributes=[];
    protected $_actions=[];

    public function init(Request $request){
        $json=json_decode($request->all()['json']);
        $params=(array)$json->vars??[];
        $this->_attributes=$params;
        foreach($params as $key=>$param){
            if(substr($key,0,1)==":"){
                $value=unserialize(base64_decode($param));
                $key=str_replace(":","",$key);
            }else{
                $value=$param;
            }
            if($this->$key!=$value){
                $this->updating($key,$value);
                $this->$key=$value;
            }
        }
        $actions=$json->actions??[];
        $this->_actions=$actions;
        foreach($actions as $action){
            $method=$action->method;
            $params=$action->params;
            $this->$method(...$params);
        }
        // Obtener atributos y valores
        $reflection=new \ReflectionClass(get_class($this));
        $attributes=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $params=[];
        foreach($attributes as $attribute){
            $params[$attribute->name]=$this->{$attribute->name};
        }
        return $this->render($params);
    }

    public function updating($name,$value){

    }

    public function syncInput($name){
        $value=$this->_attributes[$name]??$this->$name;
        $this->$name=$value;
        return $value;
    }

    public function getVars(){
        return get_object_vars($this);
    }

    public function render($params){

    }

}

?>