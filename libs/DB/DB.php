<?php

// Permite aplicar el patrón Singleton para mantener una única instancia de PDO
namespace libs\DB;

use libs\DB\Table;
use libs\DB\Flat;
use libs\Config;
use libs\Middle\Log;
use libs\Console\Console;

class DB extends \PDO{

	private static $instance=null;
	public static $sql=null;

	public static $create_db=false;

	public static function sqlQuote($value){
		return (strpos($value,".") || strpos($value,"`"))?$value:"`".$value."`";
	}

	/*
	Ejecuta una sentencia sql, puede ser metida en un try-catch y obtener el error
	una descripción del error al ejecutar la sentencia sql.
	@param string $sql -> Código sql con 
	@param array $params[] -> Parametros para la consulta sql (remplaza los ? por valores del array, de forma estructurada)
	*/
	public static function execute($sql,$params=[]){
		try{
			//echo "<pre>".print_r($sql,"<br>")."</pre>";
			//echo "<pre>".print_r($params,"<br>")."</pre>";
			Log::channel("debug","sql: ".$sql." params: ".json_encode($params)." | DB | execute()");
			$db=self::singleton();
			if($db==null){
				return null;
			}
			$query=$db->prepare($sql);
			self::$sql=$sql;
			if(!$query->execute($params)){
				throw new \Exception($query->ErrorInfo()[2]);
			}
		}catch(\Exception $ex){
			exception($ex);
		}
		return $query;
	}

	/*
	Indicar crear una nueva table o base de datos
	@param string $name_table Nombre de la tabla, sobre la que se construirá la sentencia sql.
	@return Instanciamiento de la clase Table
	*/
	public static function table($name_table){
		return new Table($name_table);
	}

	public static function singleton(){
		if(self::$instance==null){
			try{
				$database=Config::database('database');
				$connection="mysql:host=".Config::database('host').(self::$create_db?"":";port=".Config::database('port'));
				self::$instance=new \PDO($connection,Config::database('user'),Config::database('password'));
				self::$instance->query('SET NAMES '.Config::database('charset'));
				self::$instance->query('USE '.$database);
			}catch(\PDOException $ex){
				if($ex->getCode()==42000){
					Console::warning("La base de datos ".$database." no existe");
					$create=Console::ask("Desea crearla (y/n): ");
					if($create=="y"){
						try{
							self::$instance->query('CREATE DATABASE '.$database);
							self::$instance->query('USE '.$database);
							Console::success("Base de datos ".$database." creada con éxito");
						}catch(\PDOException $ex){
							Console::error("Error al crear base de datos ".$database);
							exception($ex);
						}
					}
				}else{
					exception($ex);
				}
			}
		}
		return self::$instance;
	}

	public static function getConnection(){
		return DB::singleton();
	}

	public static function flat($value){
		return new Flat($value);
	}

	public static function transation($params=[],$autocommit=true){
		$db=DB::singleton();
		$query=[];
		$db->beginTransaction();
		try{
			if($params instanceof \Closure){
				$params($db);
			}else{
				foreach($params as $param){
					$query[]=DB::execute($param['sql'],$param['params']);
				}
			}
			if($autocommit && $db->inTransaction()){
				$db->commit();
				return true;
			}
		}catch(\Exception $ex){
			if($db->inTransaction()){
				$db->rollback();
			}
			return false;
		}
	}

	private function __construct(){
		
	}

}

?>