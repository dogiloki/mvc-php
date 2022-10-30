<?php

// Es una clase que extiende PDO. Permite aplicar el patrón Singleton para mantener una única instancia de PDO

class DB extends PDO{

	private static $instance=null;
	public static $sql=null;

	// Select
	public static $column=null;
	public static $where=null;
	public static $group=null;
	public static $order=null;
	public static $having=null;
	public static $limit=null;

	// Insert
	public static $set=null;

	public const SELECT=0;
	public const INSERT=1;
	public const UPDATE=2;
	public const ALTER=3;
	public const DELETE=4;

	private function __construct(){
		
	}

	public static function singleton(){
		$config=Config::singleton();
		if(self::$instance==null){
			try{
				$connection="mysql:host=".$config->get('host').";dbname=".$config->get('db');
				self::$instance=new PDO($connection,$config->get('user'),$config->get('password'));
				self::$instance->query('SET NAMES utf8');
			}catch(PDOException $ex){
				echo $ex->getMessage();
			}
		}
		return self::$instance;
	}

	/*
	@param: db -> Conexión de la base de datos
	@param: type -> Tipo de query según las constantes de DB
	@param: sql -> Código sql con 
	@param: params -> Parametros para la consulta sql (remplaza los ? por valores del array, de forma estructurada)
	*/
	public static function execute($db,$type,$sql,$params=[]){
		$query=$db->prepare($sql);
		$query->execute($params);
		self::$sql=$sql;
		return ($type==DB::SELECT)?$query->fetchAll():$query->errorCode()<=0;
	}

	public static function selectFrom($db,$table,$params=[]){
		if(self::$column==null){
			$columns="*";
		}else{
			$columns="";
			foreach(self::$column as $column){
				$columns.=$column.",";
			}
			$columns=substr($columns,0,-1);
		}
		$where=(self::$where==null)?"":"WHERE ".self::$where;
		$group=(self::$group==null)?"":"GROUP BY ".self::$group;
		$order=(self::$order==null)?"":"ORDER BY ".self::$order;
		$having=(self::$having==null)?"":"HAVING ".self::$having;
		$limit=(self::$limit==null)?"":"LIMIT ".self::$limit;
		$sql="SELECT ".$columns." FROM ".$table." ".$where." ".$group." ".$order." ".$having." ".$limit;
		$query=$db->prepare($sql);
		$query->execute($params);
		self::$sql=$sql;
		DB::$column=null;
		DB::$where=null;
		DB::$group=null;
		DB::$order=null;
		DB::$having=null;
		DB::$limit=null;
		return $query->fetchAll();
	}

	public static function insertInto($db,$table,$params=[]){
		$sql="INSERT INTO ".$table." VALUES (";
		for($a=0; $a<sizeof($params); $a++){
			$sql.="?,";
		}
		$sql=substr($sql,0,-1);
		$sql.=")";
		$query=$db->prepare($sql);
		$query->execute($params);
		self::$sql=$sql;
		return $query;
	}

	public static function update($db,$table,$params=[]){
		$where=(self::$where==null)?"":"WHERE ".self::$where;
		$sql="UPDATE ".$table." SET ".DB::$set." ".$where;
		DB::$where=null;
		DB::$set=null;
		$query=$db->prepare($sql);
		$query->execute($params);
		self::$sql=$sql;
		return $query;
	}

	public static function delete($db,$table,$params=[]){
		$where=(self::$where==null)?"":"WHERE ".self::$where;
		$sql="DELETE FROM ".$table." ".$where;
		DB::$where=null;
		$query=$db->prepare($sql);
		$query->execute($params);
		self::$sql=$sql;
		return $query;
	}

}

?>