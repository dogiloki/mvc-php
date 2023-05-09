<?php

namespace libs\DB;

class Create extends Column{

	protected static $engine=null;
	protected static $charset=null;
	private static $type=null;
	private const TABLE=0;
	private const DATABASE=1;

	public static function database($name_db){
		DB::$create_db=true;
		$db=DB::singleton();
		DB::$sql.="CREATE DATABASE IF NOT EXISTS `".$name_db."`";
		Create::$type=Create::DATABASE;
		$query=$db->query(DB::$sql);
		$db->query("USE `".$name_db."`");
		DB::$create_db=false;
		Create::reset();
		return $query;
	}

	public static function table($name_table,$action=null){
		DB::$sql.="CREATE TABLE IF NOT EXISTS `".$name_table."`(";
		if($action instanceof \Closure){
			$action(new Column,explode("/",implode("/",array_slice(func_get_args(),2))));
		}
		Create::$type=Create::TABLE;
		return Create::execute();
	}

	public static function execute($db=null){
		$db=$db==null?DB::singleton():$db;
		foreach(Column::$Columns as $Column){
			DB::$sql.=$Column;
		}
		foreach(Column::$indexes as $index){
			DB::$sql.=$index;
		}
		$query=null;
		if(Create::$type==Create::TABLE){
			DB::$sql=substr(DB::$sql,0,strlen(DB::$sql)-1).")";
			Create::$engine??="InnoDB";
			DB::$sql.="ENGINE=".Create::$engine;
			if(Create::$charset!=null){
				DB::$sql.=" DEFAULT CHARSET=".Create::$charset;
			}
			$query=$db->prepare(DB::$sql);
			$query->execute();
		}
		Create::reset();
		return $query;
	}

	public static function reset(){
		DB::$sql="";
		Create::$engine=null;
		Create::$type=null;
		Column::reset();
	}

}

?>