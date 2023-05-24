<?php

namespace app\Components;

use libs\View\Component;

class Vista extends Component{

    public $variable="Soy una variable";
    public $search;
    public $prueba;

    public function amount(){
        
    }

    public function buscar(){
        $search=$this->search;
        $this->variable=\app\Models\User::find(function($find)use($search){
            $find->where("name","like","%{$search}%");
        },[]);
    }

    public function render($params){
        return view('components.vista',$params);
    }

}

?>