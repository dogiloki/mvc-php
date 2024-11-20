<?php

namespace libs\View;

use libs\DB\DB;

trait HasDataTable{

    private $select_columns=null;
    private $with_methods=[];
    private $current_page=1;

    public function dataTable(){
        $with_methods=$this->withMethods();
        return $this->model::each(function($each)use($with_methods){
            foreach($with_methods as $with_method){
                $each->$with_method=$each->$with_method();
            }
        })->paginate(function($query){
            $query->select($this->selectColumns());
        },10,$this->current_page);
    }

    public function selectColumns(...$columns){
        $array=func_get_args();
        if(empty($array)){
            return $this->select_columns??DB::flat("*");
        }
        $columns=is_array($array[0])?$array[0]:func_get_arg();
        return $this->select_columns=$columns??DB::flat("*");
    }

    public function withMethods($methods=null){
        if($methods==null){
            return $this->with_methods;
        }
        $array=func_get_args();
        $methods=is_array($array[0])?$array[0]:func_get_arg();
        return $this->with_methods=$methods;
    }

    public function loadPaginate($paginate){
        $this->current_page=$paginate->current_page;
    }

}

?>