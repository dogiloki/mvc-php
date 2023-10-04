<?php

namespace app\Components;

use libs\View\Component;
use app\Models\User;

class Vista extends Component{

    protected $render="components.vista";
    protected $middleware=['auth'];

    public $users=[];
    public $search="hola";
    public $live_search=false;

    public function direct(){
        return route('test');
    }

    public function updating($name,$value){
        switch($name){
            case "search":{
                if($this->syncInput('live_search')){
                    $this->search($value);
                }
                break;
            }
        }
    }

    public function search($search=null){
        $search??=$this->search;
        $this->users=User::like("name","%".$search."%")->get();
    }

}

?>