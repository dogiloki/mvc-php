<?php

// Permite aplicar el patrón Singleton para mantener una única instancia de PDO
namespace libs\DB;

use libs\DB\Table;
use libs\DB\Create;
use libs\DB\Flat;
use libs\Config;

class DB extends \PDO{

	private static $instance=null;
	public static $sql=null;

	public static $create_db=false;

	/*
	Ejecuta una sentencia sql, puede ser metida en un try-catch y obtener el error
	una descripción del error al ejecutar la sentencia sql.
	@param string $sql -> Código sql con 
	@param array $params[] -> Parametros para la consulta sql (remplaza los ? por valores del array, de forma estructurada)
	*/
	public static function execute($sql,$params=[]){
		//echo "<pre>".print_r($sql,"<br>")."</pre>";
		//echo "<pre>".print_r($params,"<br>")."</pre>";
		$db=self::singleton();
		if($db==null){
			return null;
		}
		$query=$db->prepare($sql);
		self::$sql=$sql;
		if(!$query->execute($params)){
			throw new \Exception($query->ErrorInfo()[2]);
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
				$connection="mysql:host=".env('DB_HOST').(self::$create_db?"":";port=".env('DB_PORT').";dbname=".env('DB_NAME'));
				self::$instance=new \PDO($connection,env('DB_USER'),env('DB_PASSWORD'));
				self::$instance->query('SET NAMES '.Config::database('charset'));
			}catch(\PDOException $ex){
				echo $ex->getMessage();
				return http_response_code(400);
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
			}
			return true;
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