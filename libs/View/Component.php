<?php

namespace libs\View;

abstract class Component{

    public function init($params=[]){
        $params_current=get_object_vars($this);
        foreach($params as $key=>$param){
            if(substr($key,0,1)==":"){
                $value=unserialize(base64_decode($param));
                $key=str_replace(":","",$key);
            }else{
                $value=$param;
            }
            $params_current[$key]=$value??$param;
            $this->$key=$value??$param;
        }
        $this->amount();
        $this->render(get_object_vars($this));
    }

    public function amount(){
        
    }

    public function render($params){

    }

}

?>