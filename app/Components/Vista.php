<?php

namespace app\Components;

use libs\View\Component;
use app\Models\User;

class Vista extends Component{

    protected $render="components.vista";

    public $users;
    public $search;

    public function mount(){
        if($this->search==null){
            $this->users=User::all();   
        }else{
            $this->users=User::find(function($find){ 
                $find->like("name","%".$this->search."%");
            },[]);
        }
    }

}

?>