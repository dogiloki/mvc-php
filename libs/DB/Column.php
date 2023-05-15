<?php

namespace libs\DB;

class Column{

    private $name=null;
    private $type=null;
    private $size=null;
    private $null=false;
    private $indices=[];
    private $auto_increment=false;
    private $default=null;
    private $comment=null;

    public function __construct($name, $type, $size){
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

    // Indices

    public function primaryKey(){
        return $this->primary();
    }
    public function primary(){
        $this->indices[]="PRIMARY KEY (`".$this->name."`)";
        return $this;
    }

    public function unique(){
        $this->indices[]="UNIQUE (`".$this->name."`)";
        return $this;
    }

    public function index(){
        $this->indices[]="INDEX (`".$this->name."`)";
        return $this;
    }

    public function fullText(){
        $this->indices[]="FULLTEXT (`".$this->name."`)";
        return $this;
    }

    public function foreignKey($table, $column){
        return $this->foreign($table, $column);
    }
    public function foreign($table, $column){
        $this->indices[]="FOREIGN KEY (`".$this->name."`) REFERENCES `".$table."`(`".$column."`)";
        return $this;
    }

    /// Generación del sql
    
    public function sql(){
        $sql="`".$this->name."` ".strtoupper($this->type).($this->size?"(".$this->size.")":"");
        if(!$this->null){
            $sql.=" NOT NULL";
        }
        $sql.=",";
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