<?php

namespace libs\DB;

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

	// Agrupación de columnas - GROUP BY
	private $groups=[];

	// Condicionales - HAVING
	private $havings=[];

	// Columnas de orders by y parametro
	private $orders=[];

	// Tablas y condicionales - JOIN
	private $joins=[];

	// Paginación de consulta - LIMIT
	private $limit=null;

	public function __construct($name_table=""){
		$this->name_table="`".$name_table."`";
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
		return $this->execute();
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
		return $this->execute();
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
		if(is_array($column)){
			$this->wheres[]="(";
			foreach($column as $key=>$value){
				$this->wheres[]=[
					"column"=>$key,
					"operator"=>"=",
					"value"=>$value
				];
				$this->wheres[]=" AND ";
			}
			array_pop($this->wheres);
			$this->wheres[]=")";
			return $this;
		}
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
				"operator"=>" ".$operator." ",
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

	public function group($column){
		$this->groups[]=$column;
		return $this;
	}

	public function having($column,$value){
		$args=func_get_args();
		$column=$args[0]??null;
		if($column instanceof \Closure){
			$this->havings[]="(";
			$column($this);
			$this->havings[]=")";
			return $this;
		}
		$operator=$args[1]??null;
		$value=$args[2]??null;
		if($value==null){
			$value=$operator;
			$this->havings[]=[
				"column"=>$column,
				"operator"=>"=",
				"value"=>$value
			];
		}else{
			$this->havings[]=[
				"column"=>$column,
				"operator"=>$operator,
				"value"=>$value
			];
		}
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

	public function paginate($max,$pag){
		return $this->pagination($max,$pag);
	}

	public function pagination($max,$pag){
		$index=$max*($pag-1);
		$end=$max;
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

	public function first(){
		$this->limit['index']=0;
		$this->limit['end']=1;
		$query=$this->execute();
		return $query->fetch();
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
				foreach($this->joins as $key=>$join){
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
							$this->sql.=":on_".$key;
							$params["on_".$key]=$on['value'];
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
		// Group by
		if(sizeof($this->groups)>0){
			$group_by=" GROUP BY ";
			foreach($this->groups as $group){
				$group_by.=$group.",";
			}
			$group_by=substr($group_by,0,strlen($group_by)-1);
			$this->sql.=$group_by;
		}
		// Having
		if(sizeof($this->havings)>0){
			$this->sql.=" HAVING ";
			foreach($this->havings as $key=>$having){
				if(is_array($having)){
					$this->sql.=$having['column'].$having['operator'];
					if($having['value'] instanceof Flat){
						$this->sql.=$having['value']->value;
					}else{
						$this->sql.=":having_".$key;
						$params["having_".$key]=$having['value'];
					}
				}else{
					$this->sql.=$having;
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

?>