<?php

namespace libs\DB;

use League\CommonMark\Reference\Reference;
use libs\Annotation;
use libs\DB\DB;
use libs\Config;
use libs\DB\Relation;

class Model{

	public static function __callStatic($method,$params){
		$method_query=$method;
		$method='_'.$method;
		$instace=new static;
		if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}else{
			return DB::table($instace->table)->model($instace)->$method_query(...$params);
		}
	}

	
	//private $params;
	//private $annotation_attributes;
	//private $calls=[];
	private $annotation_class;
	private $action_each=null;
	private $with_attribs=[];
	private $with_relations=[];

	protected $table=null;
	protected $primary_key="id";
	protected $hidden=[];
	protected $visible=[];

	public function __construct(){
		$reflection=new \ReflectionClass($this);
		$annotation=new Annotation($reflection->getDocComment());
		$this->annotation_class=$annotation;
		$this->table??=$annotation->get("Table");
	}

	public function __get($attrib){
		$instace=$this;
		if(method_exists($instace,$attrib)){
			$reference=call_user_func([$instace,$attrib]);
			if($reference->relation=="ManyToMany" || $reference->relation=="HasMany"){
				return $reference->query->get();
			}else{
				return $reference->query->first();
			}
		}
		return isset($this->$attrib)?$this->$attrib:null;
	}

	public function __call($method,$params){
		$method_query=$method;
		$method='_'.$method;
		$instace=$this;
		if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}else{
			return DB::table($instace->table)->model($instace)->$method_query(...$params);
		}
	}

	public function classType(){
		return get_class($this);
	}

	public function table($table){
		$this->table=$table;
		return $this;
	}

	public function _getTable(){
		return $this->table;
	}

	public function _getPrimaryKey(){
		return $this->primary_key;
	}

	public function setValues($row,$ignore_protected=false){
		if($row==null || sizeof($row)<=0){
			return;
		}
		foreach($row as $column=>$value){
			if(!$ignore_protected){
				if(count($this->visible)>0 && !in_array($column,$this->visible)){
					unset($this->$column);
					continue;
				}
				if(in_array($column,$this->hidden)){
					unset($this->$column);
					continue;
				}
			}
			$this->$column=$value;
		}
	}

	protected function hasOne($table,$id_table){
		return $this->getReference("HasOne",[$table,$id_table],$this->{$this->primary_key});
	}

	protected function hasMany($table,$id_table_secundary,$id_table=null){
		return $this->getReference("HasMany",[$table,$id_table_secundary,$id_table],$this->{$this->primary_key});
	}

	protected function manyToMany($table,$table_middle,$id_table,$id_table_middle){
		return $this->getReference("ManyToMany",[
			$table,
			$table_middle,
			$id_table,
			$id_table_middle
		],$this->{$this->primary_key});
	}

	private function getReference($annotation,$reference,$value_id){
		if(is_string($annotation)){
			$relation=$annotation;
			$model=new ($reference[0])();
		}else{
			if($annotation->get("HasOne")){
				$relation="HasOne";
			}else
			if($annotation->get("HasMany")){
				$relation="HasMany";
			}else
			if($annotation->get("ManyToMany")){
				$relation="ManyToMany";
			}
			$model=new (str_replace("/","\\",Config::filesystem('models.path'))."\\".$reference[0])();
		}
		//$attrib=$model->annotation_attributes[$reference[1]];
		if($relation=="HasOne"){
			$column=$reference[1];
			$rs=$model::select($model->getTable().".*")
			->join($this->getTable())->onColumn($this->getTable().".".$column,$model->getTable().".".$model->primary_key);
		}else
		if($relation=="HasMany"){
			$column=$reference[1];
			$rs=$model::select($model->getTable().".*")
			->join($this->getTable())->onColumn($this->getTable().".".($reference[2]??$this->primary_key),$model->getTable().".".$column);
		}
		if($relation=="ManyToMany"){
			if(is_string($annotation)){
				$model_middle=new ($reference[1])();	
			}else{
				$model_middle=new (str_replace("/","\\",Config::filesystem('models.path'))."\\".$reference[1])();
			}
			$column_middle1=$reference[2];
			$column_middle2=$reference[3];
			$rs=$model::select($model->getTable().".*")
			->join($model_middle->getTable())->on($model_middle->getTable().".".$column_middle1,$value_id)
			->whereColumn($model->getTable().".".$model->primary_key,$model_middle->getTable().".".$column_middle2);
		}
		$obj=new Relation();
		$obj->query=$rs;
		$obj->relation=$relation;
		$obj->model_primary=$this;
		$obj->model_secondary=$model;
		$obj->model_middle=$model_middle??null;
		$obj->model_primary_column=$column_middle1??null;
		$obj->model_secondary_column=$column??$column_middle2??null;
		return $obj;
	}

	public function _each($action){
		$this->action_each=$action;
		return $this;
	}

	public function _hidden(...$attribs){
		foreach($attribs as $key=>$attrib){
			$this->hidden[]=$attrib;
		}
		return $this;
	}

	public function _visible(...$attribs){
		foreach($attribs as $key=>$attrib){
			$this->visible[]=$attrib;
		}
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

	public function callExtras($model){
		foreach($this->with_attribs as $attrib){
			foreach($attrib as $key=>$value){
				$model->$key=$value;
			}
		}
		foreach($this->with_relations as $attrib){
			$attrib_split=explode(".",$attrib);
			$model_attrib=$model;
			foreach($attrib_split as $attrib2){
				if(is_array($model_attrib)){
					foreach($model_attrib as $model_attrib2){
						$model_attrib2->$attrib2=$model_attrib2->$attrib2;
					}
					$model_attrib=$model_attrib2->$attrib2;
					continue;
				}
				$model_attrib->$attrib2=$model_attrib->$attrib2;
				$model_attrib=$model_attrib->$attrib2;
			}
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
			$type=$column;
			$callback=$value;
			$model=DB::table($this->table)->model($model);
			$callback($model);
			return is_array($type)?$model->get():$model->first();
		}
		if($column==null){
			$column=$model->primary_key;
		}
		return is_array($type)?$model::where($column,$value)->get():$model::where($column,$value)->first();
	}

	public function _all(){
		return $this::select()->get();
	}

	public function _create($row,$ignore_protected=true){
		$model=$this;
		try{
			$model->setValues($row,$ignore_protected);
			$model->save();
			return $model;
		}catch(\Exception $ex){
			exception($ex);
		}
		return null;
	}

	public function update($row){
		$model=$this;
		try{
			$model->save($row);
			return $model;
		}catch(\Exception $ex){
			exception($ex);
		}
		return null;
	}

	public function save($row=null){
		try{
			$primary_key=$this->primary_key;
			$id=$this->$primary_key ;
			$params=[];
			foreach(get_object_vars($this) as $property=>$value){
				try{
					$reflection=new \ReflectionProperty($this,$property);
				}catch(\Exception $ex){
					continue;
				}
				if(!$reflection->isPublic()){
					continue;
				}
				if($value instanceof Model){
					$value=$value->{$value->primary_key};
				}else
				if(!is_array($value)){
					$params[$property]=$value;
				}	
			}
			if($id===null || $this::find($id)==null){
				$params['created_at']=date('Y-m-d H:i:s');
				$params['updated_at']=null;
				DB::table($this->table)
				->insert($params);
			}else{
				$params['updated_at']=date('Y-m-d H:i:s');
				DB::table($this->table)
				->where($primary_key,$id)
				->update($row??$params)->execute();
			}
			$row=DB::table($this->table)->select()->where($primary_key,$id??DB::getConnection()->lastInsertId())->row();
			$this->setValues($row);
			return true;
		}catch(\Exception $ex){
			exception($ex);
		}
		return false;
	}

	public function delete(){
		try{
			$primary_key=$this->primary_key;
			$id=$this->$primary_key;
			DB::table($this->table)
			->delete()
			->where($primary_key,$id)
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