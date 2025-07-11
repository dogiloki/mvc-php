<?php

namespace libs\DB;

use libs\DB\DB;
use libs\DB\Paginator;

class Table{

	// Tipos de sentencias
	private $type_query=null;
	private const INSERT="INSERT";
	private const SELECT="SELECT";
	private const UPDATE="UPDATE";
	private const DELETE="DELETE";

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

	// Clase para convertir los registro de una array asociativo a una clase
	private $model=null;

	public function __construct($name_table){
		$this->name_table=DB::sqlQuote($name_table);
	}

	/*
	Insertar registro en la table
	@param array $values_insert Puede ser un array asociativo indicand el nombre de las columnas
	@return Devuelve el el conexto actual, para ejecutar otro método compatible
	*/
	public function insert($values_insert=[],$execute=true){
		$this->type_query=self::INSERT;
		$this->sql="INSERT INTO ".$this->name_table;
		if($values_insert instanceof \Closure){
			$values_insert($this);
		}else{
			$this->values_insert=is_array($values_insert[0]??null)?$values_insert:[$values_insert];
		}
		return $execute?$this->execute():$this;
	}

	/*
	Obtener registro de la tabla
	@param array $values_select Array con el nombre de las columnas a mostra, si no es envía se mostrar todas las columnas de consulta
	@return Devuelve el el conexto actual, para ejecutar otro método compatible
	*/
	public function select($values_select=[]){
		$this->type_query=self::SELECT;
		if($this->sql==""){
			$this->sql="SELECT ";
		}
		if(is_string($values_select)){
			$values_select=explode(",",$values_select);
		}
		if($values_select instanceof \Closure){
			$values_select($this);
		}else{
			$this->values_select=array_merge($this->values_select,is_array($values_select)?$values_select:func_get_args());
		}
		return $this;
	}

	/*
	Obtener registro de la tabla
	@param array $values_update Puede ser un array asociativo indicand el nombre de las columnas y el valor a cambiar en la columna
	@return Devuelve el el conexto actual, para ejecutar otro método compatible
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
	@return Devuelve el el conexto actual, para ejecutar otro método compatible
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
	@return Devuelve el el conexto actual, para ejecutar otro método compatible
	*/
	public function onColumn(){
		$args=func_get_args();
		$column=$args[0]??null;
		$operator=$args[1]??null;
		$value=$args[2]??null;
		if($value==null){
			$value=DB::flat($operator);
			$this->on($column,$value);
		}else{
			$value=DB::flat($value);
			$this->on($column,$operator,$value);
		}
		return $this;
	}
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

	public function whereRaw($sql,$params=[]){
		$this->wheres[]=[
			"sql"=>$sql,
			"params"=>$params
		];
		return $this;
	}
	public function whereNotBetween($column,$range){
		$this->wheres[]=[
			"column"=>$column,
			"operator"=>" NOT BETWEEN ",
			"values"=>$range,
			"value_separator"=>" AND "
		];
		return $this;
	}
	public function whereBetween($column,$range){
		$this->wheres[]=[
			"column"=>$column,
			"operator"=>" BETWEEN ",
			"values"=>$range,
			"value_separator"=>" AND "
		];
		return $this;
	}
	public function whereColumn(){
		$args=func_get_args();
		$column=$args[0]??null;
		$operator=$args[1]??null;
		$value=$args[2]??null;
		if($value==null){
			$value=DB::flat($operator);
			$this->where($column,$value);
		}else{
			$value=DB::flat($value);
			$this->where($column,$operator,$value);
		}
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
	public function whereLike($column,$value){
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

	public function orderBy($column,$value){
		$this->orders[]=DB::sqlQuote($column)." ".$value;
		return $this;
	}

	public function orderByRaw($sql){
		$this->orders[]=$sql;
		return $this;
	}

	public function orderAsc($column){
		$this->orders[]=DB::sqlQuote($column)." ASC ";
		return $this;
	}

	public function orderDesc($column){
		$this->orders[]=DB::sqlQuote($column)." DESC ";
		return $this;
	}

	public function limit($index,$end=1){
		$this->limit['index']=$index;
		$this->limit['end']=$end;
		return $this;
	}

	public function pagination($max,$pag){
		$index=$max*($pag-1);
		$end=$max;
		$this->limit['index']=$index;
		$this->limit['end']=$end;
		return $this;
	}

	public function model($model){
		$this->model=$model;
		return $this;
	}

	public function exists(){
		return $this->execute()->rowCount()!=0;
	}

	public function rows(){
		return $this->execute()->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function row(){
		$this->limit['index']=0;
		$this->limit['end']=1;
		return $this->execute()->fetch(\PDO::FETCH_ASSOC);
	}

	public function get(){
		$rows=$this->rows();
		if($this->model==null){
			return $rows;
		}
		$class=[];
		foreach($rows as $row){
			$model=clone $this->model;
			$model->setValues($row);
			$model=$model->callExtras($model);
			$class[]=$model;
		}
		return $class;
	}

	public function first($call_extra=true){
		$this->limit['index']=0;
		$this->limit['end']=1;
		$row=$this->row();
		if($row==null){
			return null;
		}
		if($this->model==null){
			return $row;
		}
		$model=$this->model;
		$model->setValues($row);
		if($call_extra){
			$model=$model->callExtras($model);
		}
		return $model;
	}

	public function sql(){
		return $this->execute(false);
	}

	public function query(){
		return $this->execute();
	}

	public function execute($execute=true){
		$params=[];
		$columns="";
		$values="";
		if($this->type_query==null){
			$this->select();
		}
		switch($this->type_query){
			case self::INSERT:{
				foreach($this->values_insert as $index=>$value_insert){
					$values.="(";
					foreach($value_insert as $column=>$value){
						if($value instanceof Flat){
							$values.=$value->value.",";
							if(!is_numeric($column)){
								if($index==0){
									$columns.=DB::sqlQuote($column).",";
								}
							}
						}else{
							if(is_numeric($column)){
								$values.="?,";
							}else{
								$values.=":".$column."_".$index.",";
								if($index==0){
									$columns.=DB::sqlQuote($column).",";
								}
							}
							$params[$column."_".$index]=$value;
						}
					}
					$columns=trim($columns,",");
					$values=trim($values,",");
					$values.="),";
				}
				$values=trim($values,",");
				$columns=empty($columns)?"":"(".$columns.")";
				$this->sql.=$columns." VALUES ".$values;
				break;
			}
			case self::SELECT:{
				foreach($this->values_select as $value){
					if($value instanceof Flat){
						$value=$value->value;
						$columns.=$value.",";
					}else
					if(strpos($value,".")){
						$columns.=$value.",";
					}else{
						$columns.=DB::sqlQuote($value).",";
					}
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
						$this->sql.=" ON ".DB::sqlQuote($on['column']).$on['operator'];
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
							$columns.=DB::sqlQuote($column)."=".$value->value.",";
						}else{
							$columns.=DB::sqlQuote($column)."=:".$column.",";
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
			$key=0;
			foreach($this->wheres as $where){
				if(is_array($where)){
					if(isset($where['sql'])){
						$this->sql.=$where['sql'];
						$params=$params+$where['params'];
						continue;
					}
					$this->sql.=DB::sqlQuote($where['column']).$where['operator'];
					$values=[];
					if(isset($where['values'])){
						$values=$where['values'];
					}else
					if(isset($where['value']) || $where['value']===null){
						$values=[$where['value']];
					}
					foreach($values as $index_value=>$value){
						if($value instanceof Flat){
							$this->sql.=$value->value;
						}else{
							$this->sql.=":where_".$key;
							$params["where_".$key]=$value;
						}
						if($index_value<count($values)-1){
							$this->sql.=$where['value_separator']??"";
						}
						$key++;
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
				$group_by.=DB::sqlQuote($group).",";
			}
			$group_by=substr($group_by,0,strlen($group_by)-1);
			$this->sql.=$group_by;
		}
		// Having
		if(sizeof($this->havings)>0){
			$this->sql.=" HAVING ";
			foreach($this->havings as $key=>$having){
				if(is_array($having)){
					$this->sql.=DB::sqlQuote($having['column']).$having['operator'];
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