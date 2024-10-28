<?php

namespace libs\DB;

class Paginator{

    public $data;
    public $links;
    public $per_page=10;
    public $current_page=1;
    public $to;
    public $total;

    public function __constructor(){

    }

    public function links(){
        $links=[];
        for($index=0; $index<=$this->to+1; $index++){
            $links[]=[
                "label"=>$index==0?("&laquo;"):($index==$this->to+1?"&raquo;":$index),
                "active"=>$index==$this->current_page
            ];
        }
        $this->links=$links;
        return $this;
    }

}

?>