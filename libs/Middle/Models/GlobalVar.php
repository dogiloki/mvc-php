<?php

namespace libs\Middle\Models;

use libs\DB\Model;

class GlobalVar extends Model{

    protected $table='global_var';
    protected $primary_key='key';

    public static function __callStatic($method,$params){
        $instace=new static;
        $method='_'.$method;
        if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}
    }

    public function _lote($lote=null){
        $this->lote=$lote;
        return $this;
    }

    public function _group($lote=null){
        $this->lote=$lote;
        return $this;
    }
    
    public function _put($key,$value){
        $this->key=$key;
        $this->value=$value;
        return $this->save();
    }

    public function _get($key){
        if(isset($this->lote)){
            $this::where('lote',$this->lote)->and();
        }
        return $this::where($this->primary_key,$this->key)->row()['value']??null;
    }

    public function _has($key){
        if(isset($this->lote)){
            $this::where('lote',$this->lote)->and();
        }
        return $this::where($this->primary_key,$this->key)!=null;
    }

    public function _remove($key){
        if(isset($this->lote)){
            $this::where('lote',$this->lote)->and();
        }
        return $this::where($this->primary_key,$this->key)->delete()->execute();
    }

}

?>