<?php

namespace app\Components;

use libs\View\Component;
use app\Models\User;

class Vista extends Component{

    protected $render="components.vista";

    public $users=[];
    public $search="julio";
    public $live_search=true;
    public $temp;

    public function direct(){
        return route('test');
    }

    public function updating($name,$value){
        switch($name){
            case "search":{
                if($this->sync('live_search')){
                    $this->search($value);
                }
                break;
            }
        }
    }

    public function search($search=null){
        $search??=$this->sync('search');
        $this->sync('users',User::whereLike("name","%".$search."%")->get());
        $this->sync('temp',"-".$search."-");
    }

}

?>