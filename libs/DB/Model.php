<?php

namespace libs\DB;

use libs\Annotation;
use libs\DB\DB;

class Model{

	private $table;
	private $primary_key;
	private $class;
	private $params;
	private $annotation_class;
	private $annotation_attributes;
	private $calls=[];
	private $action_each=null;
	private $with_attribs=[];
	private $with_relations=[];

	public function __construct($class=null){
		$this->class=$class==null?$this:$class;
		$reflection=new \ReflectionClass(get_class($this->class));
		$annotation=new Annotation($reflection->getDocComment());
		$this->annotation_class=$annotation;
		$this->table=$annotation->get("Table");
		//self::$params=get_class_vars(get_class($class));
		$this->getValues();
	}

	public function __get($attrib){
		$action=$this->class->calls[$attrib]??null;
		if($action instanceof \Closure){
			return $action();
		}
		return $this->class->$attrib;
	}

	public static function __callStatic($method,$params){
		$method='_'.$method;
		$instace=new static;
		$method_query='_query';
		if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}else
		if(method_exists($instace,$method_query)){
			return call_user_func_array([$instace,$method_query],$params);
		}
	}

	public function __call($method,$params){
		$method='_'.$method;
		$instace=$this;
		if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}
	}

	public function _getTable(){
		return $this->table;
	}

	public function _getPrimaryKey(){
		return $this->primary_key;
	}

	public function _query(){
		return DB::table($this->table);
	}

	private function getValues(){
		foreach($this->class as $attrib=>$value){
			if(property_exists(get_class($this->class),$attrib)){
				$visibility=(new \ReflectionProperty(get_class($this->class),$attrib))->isPublic();
				if(!$visibility){
					continue;
				}
			}
			$prop=null;
			try{
				$prop=new \ReflectionProperty(get_class($this->class),$attrib);
			}catch(\Exception $ex){
				continue;
			}
			$annotation=new Annotation($prop->getDocComment());
			$this->annotation_attributes[$attrib]=$annotation;
			$id=$annotation->get('ID');
			if($id!=null){
				$this->primary_key=$id;
				$this->params['attributes'][$attrib]=$value;
				continue;
			}
			$column=$annotation->get('Column');
			if($column==null){
				continue;
			}
			if($value instanceof Model){
				$value=$value->class->{$value->class->primary_key};
			}
			$this->params['columns'][$attrib]=[
				'column'=>$column,
				'value'=>$value
			];
			$this->params['attributes'][$attrib]=$value;
		}
		//var_dump($this->params['columns']);
	}

	private function setValues($row){
		if($row==null || sizeof($row)<=0){
			return;
		}
		$value_id=null;
		foreach($this->params['attributes'] as $attrib=>$value){
			$value_original=$value;
			$annotation=$this->annotation_attributes[$attrib]??null;
			if($annotation==null){
				continue;
			}
			$id=$annotation->get('ID');
			$column=$annotation->get('Column');
			$relation=$annotation->get('HasOne')??$annotation->get('HasMany')??$annotation->get('ManyToMany');
			if($column==null && $id==null){
				if($relation==null){
					continue;
				}
			}
			$value=$row[$id??$column]??null;
			$value_id??=$row[$id]??null;
			//($value==null && $ignore_relation)
			if($relation==null){
				$this->class->$attrib=$value??$value_original??null;
				unset($row[$id??$column]);
			}else{
				$value_id=$row[$column]??$value_id;
				$reference=explode(',',$relation);
				$this->class->calls[$attrib]=fn()=>$this->getReference($annotation,$reference,$value_id);
				unset($this->class->$attrib);
			}
		}
		// Llenar los atributos que no estan en la clase
		// foreach($row as $column=>$value){
		// 	if(!is_numeric($column)){
		// 		$this->class->$column=$value;
		// 	}
		// }
	}

	private function getReference($annotation,$reference,$value_id){
		$value=null;
		$model=new (Config::filesystem('models')."\\".$reference[0])();
		//$attrib=$model->annotation_attributes[$reference[1]];
		$column=$reference[1];
		if($annotation->get('HasOne')!=null || $annotation->get('HasMany')!=null){
			$value=$model::find($value_id,$column,($annotation->get('HasOne')?null:[]));
		}
		if($annotation->get('ManyToMany')!=null){
			$model_middle=new (Config::filesystem('models')."\\".$reference[2])();
			//$attrib_middle=$model_middle->annotation_attributes[$reference[3]];
			$column_middle=$reference[3];
			$value=$model::find(function($find)use($model,$model_middle,$column_middle,$value_id){
				$find->select($model->getTable().".*");
				$find->join($model_middle->getTable())->on($column_middle,$value_id);
			},[]);
		}
		return $value;
	}
	
	/*
	public static function create($row){
		$self=self::class;
		$static=static::class;
		$model=new $self(new $static());
		$model->setValues($row);
		return $model->class;
	}
	*/

	public function _each($action){
		$this->action_each=$action;
		return $this;
	}

	public function _with(...$attribs){
		foreach($attribs as $key=>$attrib){
			if(is_array($attrib)){
				$this->with_attribs[]=$attrib;	
			}else{
				$this->with_relations[]=$attrib;
			}
		}
		return $this;
	}

	private function callExtras($model){
		foreach($this->with_attribs as $attrib){
			foreach($attrib as $key=>$value){
				$model->$key=$value;
			}
		}
		foreach($this->with_relations as $attrib){
			$model->$attrib=$model->$attrib;
		}
		if($this->action_each instanceof \Closure){
			$action=$this->action_each;
			$action($model);
		}
		return $model;
	}

	public function _find($value,$column=null,$type=null){
		$model=$this;
		if($value instanceof \Closure){
			$callback=$value;
			if(is_array($column)){
				$type=$column;
			}
			$find=DB::table($model->table);
			$find->select();
			$callback($find);
			$rs=$find->get();
			if($rs->rowCount()>0){
				$rows=$rs->fetchAll();
				if(is_array($type)){
					foreach($rows as $row){
						$model=new ($this->class)();
						$model->setValues($row);
						$model=$this->callExtras($model);
						$class[]=$model->class;
					}
					return $class;
				}else{
					$model->setValues($rows[0]);
					return $model->class;
				}
			}else{
				return null;
			}
		}
		if($column==null){
			$column=$model->primary_key;
		}
		try{
			$rs=null;
			if($value==null){
				return null;
			}
			$rs=DB::table($model->table)->select()
			->where($column,$value)
			->execute();
			$rows=$rs->fetchAll();
			if($rs==null || sizeof($rows)<=0){
				return null;
			}
			$model->setValues($rows[0]);
			$class=[];
			foreach($rows as $row){
				$model=new ($this->class)();
				$model->setValues($row);
				$model=$this->callExtras($model);
				$class[]=$model->class;
			}
			if(is_array($type)){
				return $class;
			}else{
				return $model->class;
			}
		}catch(\Exception $ex){
			//echo $ex->getMessage();
			throw new \Exception($ex);
			return null;
		}
	}

	public function _all(){
		$model=$this;
		try{
			$rs=DB::table($model->table)->select()->execute();
			if($rs->rowCount()>0){
				$rows=$rs->fetchAll();
				foreach($rows as $row){
					$model=new ($this->class)();
					$model->setValues($row);
					$model=$this->callExtras($model);
					$class[]=$model->class;
				}
				return $class;
			}else{
				return [];
			}
		}catch(\Exception $ex){
			//echo $ex->getMessage();
			throw new \Exception($ex);
			return [];
		}
	}

	public function _create($row){
		$model=$this;
		try{
			$model->setValues($row);
			$model->save();
			return $model->class;
		}catch(\Exception $ex){
			//echo $ex->getMessage();
			throw new \Exception($ex);
		}
		return null;
	}

	public function update($row){
		$model=$this;
		try{
			$model->save($row);
			return $model->class;
		}catch(\Exception $ex){
			//echo $ex->getMessage();
			throw new \Exception($ex);
		}
		return null;
	}

	public function save($row=null){
		try{
			$this->getValues();
			$primary_key=$this->primary_key;
			$id=null;
			$params=[];
			foreach($this->params['columns'] as $attrib=>$column){
				$value=$this->class->$attrib;
				if($value instanceof Model){
					$value=$value->class->{$value->class->primary_key};
				}
				$params[$column['column']]=$value;
			}
			if(self::find($this->class->$primary_key)==null){
				$params['created_at']=date('Y-m-d H:i:s');
				$params['updated_at']=null;
				DB::table($this->table)
				->insert($params);
			}else{
				$params['updated_at']=date('Y-m-d H:i:s');
				DB::table($this->table)
				->where($primary_key,$this->class->id)
				->update($row??$params);
				$id=$this->class->$primary_key;
			}
			$primary_key=$this->primary_key;
			$rs=DB::table($this->table)->select()->where($primary_key,$id??DB::getConnection()->lastInsertId())->execute();
			$rows=$rs->fetchAll()[0];
			$this->setValues($rows);
			return true;
		}catch(\Exception $ex){
			//echo $ex->getMessage();
			throw new \Exception($ex);
			return false;
		}
	}

	public function delete(){
		try{
			$primary_key=$this->primary_key;
			DB::table($this->table)
			->delete()
			->where($this->primary_key,$this->class->id)
			->execute();
			return true;
		}catch(\Exception $ex){
			//echo $ex->getMessage();
			throw new \Exception($ex);
			return false;
		}
	}

}

?>