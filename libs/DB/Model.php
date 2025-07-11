<?php

namespace libs\DB;

use League\CommonMark\Reference\Reference;
use libs\Annotation;
use libs\DB\DB;
use libs\Config;
use libs\DB\Relation;
use libs\DB\Paginator;

class Model{

	public static function __callStatic($method,$params){
		$method_query=$method;
		$method='_'.$method;
		$instance=new static;
		if(method_exists($instance,$method)){
			return call_user_func_array([$instance,$method],$params);
		}else{
			return DB::table($instance->table)->model($instance)->$method_query(...$params);
		}
	}

	//private $params;
	//private $annotation_attributes;
	//private $calls=[];
	private $annotation_class;
	private $action_each=null;
	private $with_attribs=[];

	protected $table=null;
	protected $primary_key="id";
	protected $fillable=[];
	protected $busable=null;
	protected $hidden=[];
	protected $visible=[];
	protected $with_relations=[];
	protected $with_methods=[];

	public function __construct(){
		$reflection=new \ReflectionClass($this);
		$annotation=new Annotation($reflection->getDocComment());
		$this->fillable[]=$this->primary_key;
		$this->annotation_class=$annotation;
		$this->table??=$annotation->get("Table");
		$this->busable??=$this->fillable;
	}

	public function __get($attrib){
		$instance=$this;
		if(method_exists($instance,$attrib)){
			$reference=call_user_func([$instance,$attrib]);
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
		$instance=$this;
		if(method_exists($instance,$method)){
			return call_user_func_array([$instance,$method],$params);
		}else{
			return DB::table($instance->table)->model($instance)->$method_query(...$params);
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

	public function getFillable(){
		return $this->fillable;
	}

	public function getFillableArray(){
		return array_flip($this->getFillable());
	}

	public function only($array){
		return array_intersect_key($array,$this->getFillableArray());
	}

	public function _getPrimaryKey(){
		return $this->primary_key;
	}

	public function setValues($row,$ignore_protected=false){
		if($row==null || sizeof($row)<=0){
			return;
		}
		foreach($row as $column=>$value){
			$this->$column=$value;
			if(!$ignore_protected){
				if(in_array($column,$this->hidden) && !in_array($column,$this->visible)){
					unset($this->$column);
				}
			}
		}
	}

	protected function hasOne($table,$id_table,$id_table_secundary=null){
		return $this->getReference("HasOne",[
			$table,
			$id_table,
			$id_table_secundary
		],$this->{$this->primary_key});
	}

	protected function hasMany($table,$id_table_secundary,$id_table=null){
		return $this->getReference("HasMany",[
			$table,
			$id_table_secundary,
			$id_table
		],$this->{$this->primary_key});
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
			->join($this->getTable())
			->onColumn($this->getTable().".".$column,$model->getTable().".".($reference[2]??$model->primary_key))
			->where($this->getTable().".".$this->primary_key,$value_id);
		}else
		if($relation=="HasMany"){
			$column=$reference[1];
			$rs=$model::select($model->getTable().".*")
			->join($this->getTable())
			->onColumn($this->getTable().".".($reference[2]??$this->primary_key),$model->getTable().".".$column)
			->where($this->getTable().".".$this->primary_key,$value_id);
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

	public function _random($count=1){
		$model=$this;
		if($count==null || $count<=0){
			return $model;
		}
		$model=$model->orderByRaw("RAND()")->limit($count);
		return $count==1?$model->first():$model->get();
		
	}

	public function _search($text){
		if($text==null || strlen($text)<=0){
			return $this;
		}
		$model=$this;
		foreach($this->busable as $key=>$value){
			$model=$model->whereLike($value,"%".$text."%");
			if($key+1<sizeof($this->busable)){
				$model=$model->or();
			}
		}
		return $model;
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
		foreach($this->with_methods as $method){
			$model->$method=$model->$method();
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
		if($row==null || sizeof($row)<=0){
			return null;
		}else{
			$row=$this->only($row);
		}
		$model=$this;
		try{
			$model->setValues($row,$ignore_protected);
			$model->save($row);
			return $model;
		}catch(\Exception $ex){
			exception($ex);
		}
		return null;
	}
	
	public function _paginate($action=null,$max=10,$pag=1){
		if(!($action instanceof \Closure)){
			$pag=$max;
			$max=$action;
			$action=null;
		}
		$max??=10;
		$pag??=1;
		$paginator=new Paginator();
		$data=$this;
		if($action!=null){
			$data=$action($data);
		}
		$data=$data->pagination($max,$pag);
		$data=$data->get();
		$paginator->data=$data;
		$paginator->results_per_page=$max;
		$paginator->current_page=$pag;
		$paginator->total_results=$this->select(DB::flat('COUNT(*) as total_results'))->first(false)->total_results;
		$paginator->total_pages=ceil($this->total_results/$max);
		$paginator->links();
		return $paginator;
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
			$id=$this->$primary_key;
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
					if($row!==null){
						if(!in_array($property,$this->fillable)){
							unset($params[$property]);
							unset($row[$property]);
						}
					}
				}
			}
			if($id===null || $this::find($id)==null){
				$params['created_at']=date('Y-m-d H:i:s');
				$params['updated_at']=null;
				$rs_insert=DB::table($this->table);
				$rs_insert->insert($params);
			}else{
				$params['updated_at']=date('Y-m-d H:i:s');
				if($row!=null){
					$row['updated_at']=$params['updated_at'];
				}
				$rs_update=DB::table($this->table);
				$rs_update->where($primary_key,$id);
				$rs_update->update($row??$params)->execute();
			}
			$this->setValues(
				DB::table($this->table)->select()->where($primary_key,$id??DB::getConnection()->lastInsertId())->row()
			);
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
			exception($ex);
			return false;
		}
	}

}

?>