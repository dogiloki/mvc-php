<?php

namespace libs\DB;

class Column{

	private static $Column_name=null;
	protected static $Columns=[];
	private static $indice_Column=null;
	private static $total_Columns=0;

	protected static $indexes=[];
	private static $total_indexes=0;

	protected static function reset(){
		Column::$Column_name=null;
		Column::$Columns=[];
		Column::$indice_Column=null;
		Column::$total_Columns=0;

		Column::$indexes=null;
		Column::$total_indexes=0;
	}

	/* Motor de almacenimiento */

	public static function engine($name){
		Create::$engine=$name;
	}

	/* charset */

	public static function charset($name){
		Create::$charset=$name;
	}

	/* Nombre y tipo de dato */

	public static function add($value,$type,$size=null){
		$Column=$value." ".strtoupper($type).($size?"(".$size.")":"")." NOT NULL,";
		Column::$Column_name=$value;
		Column::$Columns[Column::$total_Columns]=$Column;
		Column::$indice_Column=Column::$total_Columns;
		Column::$total_Columns++;
		return new Column;
	}

	/* remove, not null */

	public static function nullable(){
		$indice=Column::$indice_Column;
		$value=Column::$Columns[$indice];
		Column::$Columns[$indice]=str_replace("NOT NULL","",$value);
		return new Column;
	}

	/* auto_increment */

	public static function autoIncrement(){
		$indice=Column::$indice_Column;
		$value=Column::$Columns[$indice];
		$value=str_replace(",","",$value);
		Column::$Columns[$indice]=$value." AUTO_INCREMENT,";
		return new Column;
	}

	/* Indices */

	public static function primaryKey(){
		Column::$indexes[Column::$total_indexes]="PRIMARY KEY (".Column::$Column_name."),";
		Column::$total_indexes++;
		return new Column;
	}

	public static function foreignKey($name_table,$name_Column){
		Column::$indexes[Column::$total_indexes]="FOREIGN KEY (".Column::$Column_name.") REFERENCES `".$name_table."`(".$name_Column."),";
		Column::$total_indexes++;
		return new Column;
	}

	public static function fullText(){
		Column::$indexes[Column::$total_indexes]="FULLTEXT(".Column::$Column_name."),";
		Column::$total_indexes++;
		return new Column;
	}

	public static function unique(){
		Column::$indexes[Column::$total_indexes]="UNIQUE(".Column::$Column_name."),";
		Column::$total_indexes++;
		return new Column;
	}

	public static function index(){
		Column::$indexes[Column::$total_indexes]="INDEX(".Column::$Column_name."),";
		Column::$total_indexes++;
		return new Column;
	}

}

?>