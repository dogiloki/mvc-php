<?php

namespace libs\DB;

use libs\DB\Column;
use libs\Config;

class Schema{

    // Tipos de sentencias
	private $type_query=null;
	private const CREATE=0;
    private const CREATE_IF_NOT_EXISTS=1;
    private const ALTER=[
        'ADD'=>1,
        'CHANGE'=>2,
        'DROP'=>3,
        'MODIFY'=>4
    ];

    private $engine=null;
    private $charset=null;
    private $name_table=null;
    private $columns=[];
    private $prev_column=null;
    private $sql=null;

    public function __construct(){
		
	}

    public function table($name_table, $action){
        $this->engine=Config::database('engine');
        $this->charset=Config::database('charset');
        $this->name_table="`".$name_table."`";
        if($action instanceof \Closure){
            $action($this,explode("/",implode("/",array_slice(func_get_args(),2))));
        }
        return $this->execute();
    }

    public function tableIfNotExists($name_table, $action){
        $this->type_query=self::CREATE_IF_NOT_EXISTS;
        return $this->table($name_table,$action);
    }

    /**
     * Motor de almacenamiento
     * @param string $name
     */
	public function engine($name){
		$this->engine=$name;
	}

    /**
     * Tipo de charset
     * @param string $name
     */
	public function charset($name){
		$this->charset=$name;
	}

    /**
     * Agregar columna
     * @param string $column Nombre de la columna
     * @param string $type Tipo de dato
     * @param int $size Tamaño del dato
     */
    public function add($name, $type, $size=null){
        $this->type_query??=self::CREATE;
        if($type=="varchar"){
            $size=255;
        }
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
        $this->add('updated_at','timestamp')->default('CURRENT_TIMESTAMP')->nullable()->index();
    }

    /**
     * Eliminar columna
     * @param string $column Nombre de la columna
     */
    public function dropColumn($column){
        $this->type_query=self::ALTER['DROP'];
        $column=new Column($column);
        $this->columns[]=$column;
        $this->prev_column=$column;
        return $column;
    }

    /**
     * Elimina una tabla de la base de datos si existe
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
     * Elimina una tabla de la base de datos (da error si no existe)
     * @param string $table Nombre de la tabla a eliminar
     */
    public function drop(string $table){
        try{
            $sql="DROP TABLE `$table"."`";
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
        $sql="";
        switch($this->type_query){
            case self::CREATE_IF_NOT_EXISTS:
            case self::CREATE:{
                $sql=($this->type_query==self::CREATE?"CREATE TABLE ":"CREATE TABLE IF NOT EXISTS ").$this->name_table." ";
                $sql.="(";
                foreach($this->columns as $column){
                    $sql.=$column->sql();
                }
                $sql=substr($sql,0,strlen($sql)-1);
                $sql.=")";
                $sql.="ENGINE=".$this->engine;
                $sql.=" DEFAULT CHARSET=".$this->charset;
                break;
            }
            case self::ALTER['DROP']:{
                $sql="ALTER TABLE ".$this->name_table." DROP COLUMN ";
                foreach($this->columns as $column){
                    $column->nullable();
                    $sql.=$column->sql();
                }
                $sql=substr($sql,0,strlen($sql)-1);
                break;
            }
        }
        $this->sql.=$sql;
        $query=DB::execute($this->sql);
        $this->sql=null;
        return $query;
    }



}

?>