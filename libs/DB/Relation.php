<?php

namespace libs\DB;

use libs\DB\DB;
use libs\DB\Model;

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
        $ids=is_array($ids)?$ids:func_get_args();
        $data=[];
        if($this->relation=="ManyToMany"){
            foreach($ids as $id){
                $data[]=[
                    $this->model_primary_column=>$this->model_primary->{$this->model_primary->primary_key},
                    $this->model_secondary_column=>$id
                ];
            }
            return DB::table($this->model_middle->getTable())
            ->insert($data);
        }
    }

    public function detach($ids){
        $ids=is_array($ids)?$ids:func_get_args();
        if($this->relation=="ManyToMany"){
            $rs=DB::table($this->model_middle->getTable())->delete();
            foreach($ids as $index=>$id){
                $rs->where(function($rs_where)use($id){
                    $rs_where->where($this->model_primary_column,$this->model_primary->{$this->model_primary->primary_key})->and()
                    ->where($this->model_secondary_column,$id);
                });
                if($index<count($ids)-1){
                    $rs->or();
                }
            }
            return $rs->execute();
        }
    }

    public function sync($ids){
        $ids=is_array($ids)?$ids:func_get_args();
        if($this->relation=="ManyToMany"){
            $db=DB::singleton();
            try{
                $db->beginTransaction();
                $rs=DB::table($this->model_middle->getTable());
                $rs->where($this->model_primary_column,$this->model_primary->{$this->model_primary->primary_key});
                $rs->delete()->execute();
                if($this->attach($ids)){
                    $db->commit();
                    return true;
                }
                throw new \Exception();
            }catch(\Exception $ex){
                if($db->inTransaction()){
                    $db->rollback();
                }
            }
            return false;
        }
    }

    public function exists($ids=null){
        $ids=is_array($ids)?$ids:func_get_args();
        $rs=DB::table($this->model_middle->getTable())->select();
        if($this->relation=="ManyToMany"){
            foreach($ids as $index=>$id){
                $rs->where(function($rs_where)use($id){
                    $rs_where->where($this->model_primary_column,$this->model_primary->{$this->model_primary->primary_key})->and()
                    ->where($this->model_secondary_column,$id);
                });
                if($index<count($ids)-1){
                    $rs->or();
                }
            }
            return $rs->exists();
        }
    }

    public function associate($model){
        $id=($model instanceof Model)?$model->{$model->primary_key}:$model;
        $this->model_primary->{$this->model_secondary_column}=$id;
    }

    public function dissociate(){
        $this->model_primary->{$this->model_secondary_column}=null;
    }

}

?>