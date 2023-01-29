<?php

// Permite aplicar el patrón Singleton para mantener una única instancia de PDO
namespace libs;

class DB{

	private static $instance=null;
	public static $sql=null;

	public static $create_db=false;

	private function __construct(){
		
	}

	public static function singleton(){
		$config=Config::singleton();
		if(self::$instance==null){
			try{
				$connection="mysql:host=".$config->get('DB_HOST').(self::$create_db?"":";port=".$config->get('DB_PORT').";dbname=".$config->get('DB_NAME'));
				self::$instance=new \PDO($connection,$config->get('DB_USER'),$config->get('DB_PASSWORD'));
				self::$instance->query('SET NAMES utf8');
			}catch(\PDOException $ex){
				echo $ex->getMessage();
			}
		}
		return self::$instance;
	}

	public static function flat($value){
		return new Flat($value);
	}

	public static function transation($params=[],$autocommit=true){
		$db=DB::singleton();
		$query=[];
		$db->beginTransaction();
		try{
			foreach($params as $param){
				$query[]=DB::execute($param['sql'],$param['params']);
			}
			if($autocommit){
				$db->commit();
			}
			return true;
		}catch(\Exception $ex){
			$db->rollback();
			throw new \Exception($ex->getMessage());
			return false;
		}
	}

	public static function beginTransaction(){
		Db::singleton()->beginTransaction();
	}

	public static function commit(){
		DB::singleton()->commit();
	}

	public static function rollback(){
		DB::singleton()->rollback();
	}

	public static function lastInsertId(){
		return DB::singleton()->lastInsertId();
	}

	/*
	Ejecuta una sentencia sql, puede ser metida en un try-catch y obtener el error
	una descripción del error al ejecutar la sentencia sql.
	@param string $sql -> Código sql con 
	@param array $params[] -> Parametros para la consulta sql (remplaza los ? por valores del array, de forma estructurada)
	*/
	public static function execute($sql,$params=[]){
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
	@return Instanciamiento de la clase Create
	*/
	public static function create(){
		return new Create;
	}

	/*
	Indicar crear una nueva table o base de datos
	@param string $name_table Nombre de la tabla, sobre la que se construirá la sentencia sql.
	@return Instanciamiento de la clase Table
	*/
	public static function table($name_table){
		return new Table($name_table);
	}

}

class Flat{

	public function __construct($value){
		$this->value=$value;
	}

}

class Table{

	// Tipos de sentencias
	private $type_query=null;
	private const INSERT=0;
	private const SELECT=1;
	private const UPDATE=2;
	private const DELETE=3;

	// Código sql
	public $sql="";

	// Nombre de la tabla
	private $name_table="";

	// Parametros de insert
	private $values_insert=[];

	// Parametros de select
	private $values_select=[];

	// Columnas
	private $values_update=[];

	// Condicionales - WHERE y LIKE
	private $wheres=[];

	// Columnas de orders by y parametro
	private $orders=[];

	// Tablas y condicionales - JOIN
	private $joins=[];

	// Paginación de consulta - LIMIT
	private $limit=null;

	public function __construct($name_table=""){
		$this->name_table=$name_table;
	}

	/*
	Insertar registro en la table
	@param array $values_insert Puede ser un array asociativo indicand el nombre de las columnas
	@return Devuelve el el conexto actual, para ejecutar otro metodo compatible
	*/
	public function insert($values_insert=[]){
		$this->type_query=self::INSERT;
		$this->sql="INSERT INTO ".$this->name_table;
		if($values_insert instanceof \Closure){
			$values_insert($this);
		}else{
			$this->values_insert=is_array($values_insert)?$values_insert:func_get_args();
		}
		return $this;
	}

	/*
	Obtener registro de la tabla
	@param array $values_select Array con el nombre de las columnas a mostra, si no es envía se mostrar todas las columnas de consulta
	@return Devuelve el el conexto actual, para ejecutar otro metodo compatible
	*/
	public function select($values_select=[]){
		$this->type_query=self::SELECT;
		$this->sql="SELECT ";
		if($values_select instanceof \Closure){
			$values_select($this);
		}else{
			$this->values_select=is_array($values_select)?$values_select:func_get_args();
		}
		return $this;
	}

	/*
	Obtener registro de la tabla
	@param array $values_update Puede ser un array asociativo indicand el nombre de las columnas y el valor a cambiar en la columna
	@return Devuelve el el conexto actual, para ejecutar otro metodo compatible
	*/
	public function update($values_update=[]){
		$this->type_query=self::UPDATE;
		$this->sql="UPDATE ".$this->name_table;
		if($values_update instanceof \Closure){
			$values_update($this);
		}else{
			$this->values_update=is_array($values_update)?$values_update:func_get_args();
		}
		return $this;
	}

	/*
	Elimina registro de la tabla
	*/
	public function delete(){
		$this->type_query=self::DELETE;
		$this->sql="DELETE FROM ".$this->name_table;
		return $this;
	}

	/*
	Indica que se unirá una table en la consulta (select)
	@param string $table Nombre de la tabla
	@return Devuelve el el conexto actual, para ejecutar otro metodo compatible
	*/
	public function join($table){
		$this->joins[]=[
			"type"=>" JOIN ",
			"table"=>$table,
			"where"=>null
		];
		return $this;
	}
	public function leftJoin($table){
		$this->joins[]=[
			"type"=>" LEFT JOIN ",
			"table"=>$table,
			"where"=>null
		];
		return $this;
	}
	public function rightJoin($table){
		$this->joins[]=[
			"type"=>" RIGHT JOIN ",
			"table"=>$table,
			"where"=>null
		];
		return $this;
	}
	public function innerJoin($table){
		$this->joins[]=[
			"type"=>" INNER JOIN ",
			"table"=>$table,
			"where"=>null
		];
		return $this;
	}

	/*
	Indica la condición que debe cumplir después de unir [ join() ] una tabla
	@param string Nombre de la columna
	@param string operador Operación aritmética (opcional)
	@param primitive Nombre del valor a comparar
	@return Devuelve el el conexto actual, para ejecutar otro metodo compatible
	*/
	public function on(){
		$args=func_get_args();
		$column=$args[0]??null;
		$operator=$args[1]??null;
		$value=$args[2]??null;
		$index=sizeof($this->joins);
		$value_temp=$value;
		if($value==null){
			$value=$operator;
		}
		$this->joins[$index-1]['where']=(sizeof($args)<=1)?$column:[
			"column"=>$column,
			"operator"=>($value_temp==null)?"=":$operator,
			"value"=>$value
		];
		return $this;
	}

	public function where(){
		$args=func_get_args();
		$column=$args[0]??null;
		if($column instanceof \Closure){
			$this->wheres[]="(";
			$column($this);
			$this->wheres[]=")";
			return $this;
		}
		$operator=$args[1]??null;
		$value=$args[2]??null;
		if($value==null){
			$value=$operator;
			$this->wheres[]=[
				"column"=>$column,
				"operator"=>"=",
				"value"=>$value
			];
		}else{
			$this->wheres[]=[
				"column"=>$column,
				"operator"=>$operator,
				"value"=>$value
			];
		}
		return $this;
	}
	public function and(){
		$this->wheres[]=" AND ";
		return $this;
	}
	public function or(){
		$this->wheres[]=" OR ";
		return $this;
	}
	public function like($column,$value){
		$this->wheres[]=[
			"column"=>$column,
			"operator"=>" LIKE ",
			"value"=>$value
		];
		return $this;
	}

	public function orderAsc($column){
		$this->orders[]=$column." ASC ";
		return $this;
	}

	public function orderDesc($column){
		$this->orders[]=$column." DESC ";
		return $this;
	}

	public function pagination($max,$pag){
		$index=$max*($pag-1);
		$end=$index+$max;
		$this->limit['index']=$index;
		$this->limit['end']=$end;
		return $this;
	}

	public function sql(){
		return $this->execute(false);
	}

	public function get(){
		return $this->execute();
	}

	public function execute($execute=true){
		$params=[];
		$columns="";
		$values="";
		switch($this->type_query){
			case self::INSERT:{
				foreach($this->values_insert as $column=>$value){
					if($value instanceof Flat){
						$values.=$value->value.",";
						if(!is_numeric($column)){
							$columns.=$column.",";
						}
					}else{
						if(is_numeric($column)){
							$values.="?,";
						}else{
							$values.=":".$column.",";
							$columns.=$column.",";
						}
						$params[$column]=$value;
					}
				}
				$columns=trim($columns,",");
				$values=trim($values,",");
				$columns=empty($columns)?"":"(".$columns.")";
				$this->sql.=$columns." VALUES (".$values.")";
				break;
			}
			case self::SELECT:{
				foreach($this->values_select as $value){
					$columns.=$value.",";
				}
				$columns=empty($columns)?"*":$columns;
				$columns=trim($columns,",");
				$this->sql.=$columns." FROM ".$this->name_table;
				// Join
				foreach($this->joins as $join){
					$on=$join['where'];
					$this->sql.=$join['type'].$join['table'];
					if($on==null){
						continue;
					}
					if(is_array($on)){
						$this->sql.=" ON ".$on['column'].$on['operator'];
						if($on['value'] instanceof Flat){
							$this->sql.=$on['value']->value." ";
						}else{
							$this->sql.="? ";
							$params[]=$on['value'];
						}
					}else{
						$this->sql.=" ON ".$on;
					}
				}
				break;
			}
			case self::UPDATE:{
				$this->sql.=" SET ";
				$columns="";
				foreach($this->values_update as $column=>$value){
					if(!is_numeric($column)){
						if($value instanceof Flat){
							$columns.=$column."=".$value->value.",";
						}else{
							$columns.=$column."=:".$column.",";
							$params[$column]=$value;
						}
					}
				}
				$this->sql.=trim($columns,",");
				break;
			}
		}
		// Where
		if(sizeof($this->wheres)>0){
			$this->sql.=" WHERE ";
			foreach($this->wheres as $key=>$where){
				if(is_array($where)){
					$this->sql.=$where['column'].$where['operator'];
					if($where['value'] instanceof Flat){
						$this->sql.=$where['value']->value;
					}else{
						$this->sql.=":where_".$key;
						$params["where_".$key]=$where['value'];
					}
				}else{
					$this->sql.=$where;
				}
			}
		}
		// Order by
		if(sizeof($this->orders)>0){
			$this->sql.=" ORDER BY ";
			$orders="";
			foreach($this->orders as $order){
				$orders.=$order.",";
			}
			$orders=trim($orders,",");
			$this->sql.=$orders;
		}
		// Limit
		if($this->limit!=null){
			$this->sql.=" LIMIT ".$this->limit['index'].",".$this->limit['end'];
		}
		$params=array_merge($params);
		$this->sql=trim($this->sql,",");
		if(!$execute){
			return [
				'sql'=>$this->sql,
				'params'=>$params
			];
		}else{
			$query=DB::execute($this->sql,$params);
		}
		return $query;
	}

}

class Create extends Column2{

	protected static $engine=null;
	protected static $charset=null;
	private static $type=null;
	private const TABLE=0;
	private const DATABASE=1;

	public static function database($name_db){
		DB::$create_db=true;
		$db=DB::singleton();
		DB::$sql.="CREATE DATABASE IF NOT EXISTS ".$name_db;
		Create::$type=Create::DATABASE;
		$query=$db->query(DB::$sql);
		$db->query('USE '.$name_db);
		DB::$create_db=false;
		Create::reset();
		return $query;
	}

	public static function table($name_table,$action=null){
		DB::$sql.="CREATE TABLE IF NOT EXISTS ".$name_table."(";
		if($action instanceof \Closure){
			$action(new Column2,explode("/",implode("/",array_slice(func_get_args(),2))));
		}
		Create::$type=Create::TABLE;
		return Create::execute();
	}

	public static function execute($db=null){
		$db=$db==null?DB::singleton():$db;
		foreach(Column2::$Column2s as $Column2){
			DB::$sql.=$Column2;
		}
		foreach(Column2::$indexes as $index){
			DB::$sql.=$index;
		}
		$query=null;
		if(Create::$type==Create::TABLE){
			DB::$sql=substr(DB::$sql,0,strlen(DB::$sql)-1).")";
			if(Create::$engine!=null){
				DB::$sql.="ENGINE=".Create::$engine;
			}
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
		Column2::reset();
	}

}

class Column2{

	private static $Column2_name=null;
	protected static $Column2s=[];
	private static $indice_Column2=null;
	private static $total_Column2s=0;

	protected static $indexes=[];
	private static $total_indexes=0;

	protected static function reset(){
		Column2::$Column2_name=null;
		Column2::$Column2s=[];
		Column2::$indice_Column2=null;
		Column2::$total_Column2s=0;

		Column2::$indexes=null;
		Column2::$total_indexes=0;
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
		$Column2=$value." ".strtoupper($type).($size?"(".$size.")":"")." NOT NULL,";
		Column2::$Column2_name=$value;
		Column2::$Column2s[Column2::$total_Column2s]=$Column2;
		Column2::$indice_Column2=Column2::$total_Column2s;
		Column2::$total_Column2s++;
		return new Column2;
	}

	/* remove, not null */

	public static function nullable(){
		$indice=Column2::$indice_Column2;
		$value=Column2::$Column2s[$indice];
		Column2::$Column2s[$indice]=str_replace("NOT NULL","",$value);
		return new Column2;
	}

	/* auto_increment */

	public static function autoIncrement(){
		$indice=Column2::$indice_Column2;
		$value=Column2::$Column2s[$indice];
		$value=str_replace(",","",$value);
		Column2::$Column2s[$indice]=$value." AUTO_INCREMENT,";
		return new Column2;
	}

	/* Indices */

	public static function primaryKey(){
		Column2::$indexes[Column2::$total_indexes]="PRIMARY KEY (".Column2::$Column2_name."),";
		Column2::$total_indexes++;
		return new Column2;
	}

	public static function foreignKey($name_table,$name_Column2){
		Column2::$indexes[Column2::$total_indexes]="FOREIGN KEY (".Column2::$Column2_name.") REFERENCES ".$name_table."(".$name_Column2."),";
		Column2::$total_indexes++;
		return new Column2;
	}

	public static function fullText(){
		Column2::$indexes[Column2::$total_indexes]="FULLTEXT(".Column2::$Column2_name."),";
		Column2::$total_indexes++;
		return new Column2;
	}

	public static function unique(){
		Column2::$indexes[Column2::$total_indexes]="UNIQUE(".Column2::$Column2_name."),";
		Column2::$total_indexes++;
		return new Column2;
	}

	public static function index(){
		Column2::$indexes[Column2::$total_indexes]="INDEX(".Column2::$Column2_name."),";
		Column2::$total_indexes++;
		return new Column2;
	}

}

?>