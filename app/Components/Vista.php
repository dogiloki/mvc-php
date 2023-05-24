<?php

namespace app\Components;

use libs\View\Component;
use libs\Middle\Log;

class Vista extends Component{

    public $variable=[];
    public $search;
    public $live_search=false;

    public function updating($name,$value){
        switch($name){
            case "search":{
                if($this->syncInput('live_search')){
                    $this->buscar($value);
                }
                break;
            }
        }
    }

    public function buscar($search=null){
        $search??=$this->search;
        $this->variable=\app\Models\User::find(function($find)use($search){
            $find->where("name","like","%{$search}%");
        },[]);
    }

    public function render($params){
        return view('components.vista',$params);
    }

}

?>