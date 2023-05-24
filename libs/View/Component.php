<?php

namespace libs\View;

use libs\Http\Request;

abstract class Component{

    public function init(Request $request){
        $json=json_decode($request->all()['json']);
        $wires=call_user_func_array('array_merge',array_map(function($wire){
            $array[$wire->attrib]=$wire->value;
            return $array;
        },$json->wires??[]));
        $params=array_merge((array)$json->vars??[],(array)$wires);
        foreach($params as $key=>$param){
            if(substr($key,0,1)==":"){
                $value=unserialize(base64_decode($param));
                $key=str_replace(":","",$key);
            }else{
                $value=$param;
            }
            $this->$key=$value;
        }
        $actions=$json->actions??[];
        foreach($actions as $action){
            $method=$action->method;
            $params=$action->params;
            $this->$method(...$params);
        }
        $this->amount();
        // Obtener atributos y valores
        $reflection=new \ReflectionClass(get_class($this));
        $attributes=$reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $params=[];
        foreach($attributes as $attribute){
            $params[$attribute->name]=$this->{$attribute->name};
        }
        return $this->render($params);
    }

    public function getVars(){
        return get_object_vars($this);
    }

    public function amount(){
        
    }

    public function render($params){

    }

}

?>