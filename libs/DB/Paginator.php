<?php

namespace libs\DB;

class Paginator{

    public $data;
    public $links;
    public $results_per_page=10;
    public $current_page=1;
    public $total_pages;
    public $total_results;
    public $info;

    public function __constructor(){

    }

    public function links(){
        $links=[];
        for($index=0; $index<=$this->total_pages+1; $index++){
            $links[]=[
                "label"=>$index==0?__("pagination.prev"):($index==$this->total_pages+1?__("pagination.next"):$index),
                "value"=>$index==0?-1:($index==$this->total_pages+1?0:$index),
                "active"=>$index==$this->current_page
            ];
        }
        $this->links=$links;
        $this->info=__("pagination.info",[
            "results_per_page"=>$this->results_per_page,
            "current_page"=>$this->current_page,
            "total_pages"=>$this->total_pages,
            "total_results"=>$this->total_results
        ]);
        return $this;
    }

}

?>