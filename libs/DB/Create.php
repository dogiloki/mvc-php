<?php

namespace libs\DB;

use libs\DB\Column;
use libs\Middle\Middle;

class Create{

    private $engine=null;
    private $charset=null;
    private $name_table=null;
    private $columns=[];
    private $prev_column=null;
    private $sql="";

    public function __construct(){
		
	}

    public function table($name_table, $action){
        $this->engine=Middle::config('database','engine');
        $this->charset=Middle::config('database','charset');
        $this->name_table="`".$name_table."`";
        $this->sql="CREATE TABLE IF NOT EXISTS ".$this->name_table;
        if($action instanceof \Closure){
            $action($this,explode("/",implode("/",array_slice(func_get_args(),2))));
        }
        return $this->execute();
    }

    /**
     * Motor de almacenamiento
     * @param string $name
     */
	public function engine($name){
		$this->$engine=$name;
	}

    /**
     * Tipo de charset
     * @param string $name
     */
	public function charset($name){
		$this->$charset=$name;
	}

    /**
     * Agregar columna
     * @param string $column Nombre de la columna
     * @param string $type Tipo de dato
     * @param int $size Tamaño del dato
     */
    public function add($name, $type, $size=null){
        $column=new Column($name, $type, $size);
        $this->columns[]=$column;
        $this->prev_column=$column;
        return $column;
    }

    // Funcionalidades

    public function id(){
        $this->add('id','bigint')->primary()->autoIncrement();
    }

    /*
    public function softDelete(){
        $this->add('deleted_at','timestamp')->nullable()->index();
    }
    */

    public function idForeign($name_table){
        return $this->add($name_table,'bigint');
    }

    public function timestamps(){
        $this->add('created_at','timestamp')->default('CURRENT_TIMESTAMP')->index();
        $this->add('updated_at','timestamp')->nullable()->index();
    }

    /**
     * Elimina una tabla de la base de datos
     * @param string $table Nombre de la tabla a eliminar
     */
    public function dropIfExists(string $table){
        try{
            $sql="DROP TABLE IF EXISTS `$table"."`";
            $this->sql=$sql;
            DB::execute($this->sql);
        }catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
        }
    }

    /**
     * Ejecutar sql
     */
    public function execute(){
        $sql="(";
        foreach($this->columns as $column){
            $sql.=$column->sql();
        }
        $sql=substr($sql,0,strlen($sql)-1);
        $sql.=")";
        $sql.="ENGINE=".$this->engine;
        $sql.=" DEFAULT CHARSET=".$this->charset;
        $this->sql.=$sql;
        return DB::execute($this->sql);
    }



}

?>