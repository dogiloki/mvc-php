<?php

namespace libs\DB;

use libs\DB\DB;
use libs\DB\Flat;

class Column{

    private $name=null;
    private $type=null;
    private $size=null;
    private $null=false;
    private $indices=[];
    private $auto_increment=false;
    private $default=null;
    private $comment=null;

    public function __construct($name,$type="",$size=null){
        $this->name=$name;
        $this->type=$type;
        $this->size=$size;
    }

    public function nullable(){
        $this->null=true;
        return $this;
    }

    public function autoIncrement(){
        $this->auto_increment=true;
        return $this;
    }

    public function default($value){
        $this->default=($value instanceof Flat)?($value->value):($value==null?$value:"'".$value."'");
        return $this;
    }

    public function comment($value){
        $this->comment=($value instanceof Flat)?($value->value):($value==null?$value:"'".$value."'");
        return $this;
    }

    // Indices

    public function primaryKey(){
        return $this->primary();
    }
    public function primary(){
        $this->indices[]="PRIMARY KEY (".DB::sqlQuote($this->name).")";
        return $this;
    }

    public function unique(){
        $this->indices[]="UNIQUE (".DB::sqlQuote($this->name).")";
        return $this;
    }

    public function index(){
        $this->indices[]="INDEX (".DB::sqlQuote($this->name).")";
        return $this;
    }

    public function fullText(){
        $this->indices[]="FULLTEXT (".DB::sqlQuote($this->name).")";
        return $this;
    }

    public function foreignKey($table,$column="id"){
        return $this->foreign($table,$column);
    }
    public function foreign($table,$column){
        $this->indices[]="FOREIGN KEY (".DB::sqlQuote($this->name).") REFERENCES ".DB::sqlQuote($table)."(".DB::sqlQuote($column).")";
        return $this;
    }

    // Generación del sql
    
    public function sql(){
        $sql=DB::sqlQuote($this->name)." ".strtoupper($this->type).($this->size?"(".$this->size.")":"");
        if(!$this->null){
            $sql.=" NOT NULL";
        }
        if($this->default){
            $sql.=" DEFAULT ".$this->default;
        }
        $sql.=",";
        if($this->comment){
            $sql.=" -- ".$this->comment;
        }
        if($this->auto_increment){
            $sql=substr($sql,0,strlen($sql)-1);
            $sql.=" AUTO_INCREMENT,";
        }
        foreach($this->indices as $indice){
            $sql.=" ".$indice.",";
        }
        return $sql;
    }

}

?>