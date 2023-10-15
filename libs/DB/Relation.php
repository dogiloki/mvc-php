<?php

namespace libs\DB;

use libs\DB\DB;

class Relation{

    public $query;
    public $relation;
    public $model_primary;
    public $model_secondary;
    public $model_middle;
    public $model_primary_column;
    public $model_secondary_column;

    public function __construct(){
        
    }

    public function attach($ids){
        $data=[];
        if($this->relation=="ManyToMany"){
            foreach($ids as $id){
                $data[]=[
                    $this->model_primary_column=>$this->model_primary->class->{$this->model_primary->class->primary_key},
                    $this->model_secondary_column=>$id
                ];
            }
            return DB::table($this->model_middle->getTable())
            ->insert($data)->execute();
        }
    }

    public function detach($ids){
        $data=[];
        if($this->relation=="ManyToMany"){
            $rs=DB::table($this->model_middle->getTable())->delete();
            foreach($ids as $index=>$id){
                $rs->where(function($rs_where)use($id){
                    $rs_where->where($this->model_primary_column,$this->model_primary->class->{$this->model_primary->class->primary_key})->and()
                    ->where($this->model_secondary_column,$id);
                });
                if($index<count($ids)-1){
                    $rs->or();
                }
            }
            return $rs->execute();
        }
    }

}

?>