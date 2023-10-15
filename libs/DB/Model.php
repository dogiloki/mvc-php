<?php

namespace libs\DB;

use libs\Annotation;
use libs\DB\DB;
use libs\Config;

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

	protected $hidden=[];
	protected $visible=[];

	public function __construct($class=null,$table=null){
		$this->class=$class==null?$this:$class;
		$reflection=new \ReflectionClass(get_class($this->class));
		$annotation=new Annotation($reflection->getDocComment());
		$this->annotation_class=$annotation;
		$this->table=$table??$annotation->get("Table");
		//self::$params=get_class_vars(get_class($class));
		$this->getValues();
	}

	public function __get($attrib){
		$instace=$this;
		if(method_exists($instace,$attrib)){
			$reference=call_user_func([$instace,$attrib]);
			if($reference['relation']=="ManyToMany"){
				return $reference['rs']->get();
			}else{
				return $reference['rs']->first();
			}
		}
		$action=$this->class->calls[$attrib]??null;
		if($action instanceof \Closure){
			return $action();
		}
		return $this->class->$attrib;
	}

	public static function __callStatic($method,$params){
		$method_query=$method;
		$method='_'.$method;
		$instace=new static;
		if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}else{
			return DB::table($instace->table)->select()->model($instace->class)->$method_query(...$params);
		}
	}

	public function __call($method,$params){
		$method_query=$method;
		$method='_'.$method;
		$instace=$this;
		if(method_exists($instace,$method)){
			return call_user_func_array([$instace,$method],$params);
		}else{
			return DB::table($instace->table)->select()->model($instace->class)->$method_query(...$params);
		}
	}

	public function classType(){
		return get_class($this->class);
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
			$column=$annotation->get('Column')??$attrib;
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

	public function setValues($row,$ignore_protected=false){
		if($row==null || sizeof($row)<=0){
			return;
		}
		$value_id=null;
		foreach($this->params['attributes'] as $attrib=>$value){
			if(!$ignore_protected){
				if(count($this->visible)>0 && !in_array($attrib,$this->visible)){
					unset($this->class->$attrib);
					continue;
				}
				if(in_array($attrib,$this->hidden)){
					unset($this->class->$attrib);
					continue;
				}
			}
			$value_original=$value;
			$annotation=$this->annotation_attributes[$attrib]??null;
			if($annotation==null){
				continue;
			}
			$id=$annotation->get('ID');
			$column=$annotation->get('Column')??$attrib;
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

	protected function hasOne($table,$id_table){
		return $this->getReference("HasOne",[$table,$id_table],$this->class->{$this->class->primary_key});
	}

	protected function hasMany($table,$id_table){
		return $this->getReference("HasMany",[$table,$id_table],$this->class->{$this->class->primary_key});
	}

	protected function manyToMany($table,$table_middle,$id_table,$id_table_middle){
		return $this->getReference("ManyToMany",[
			$table,
			$table_middle,
			$id_table,
			$id_table_middle
		],$this->class->{$this->class->primary_key});
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
			if($annotation->get('ManyToMany')){
				$relation="ManyToMany";
			}
			$model=new (str_replace("/","\\",Config::filesystem('models.path'))."\\".$reference[0])();
		}
		//$attrib=$model->annotation_attributes[$reference[1]];
		if($relation=="HasOne" || $relation=="HasMany"){
			$rs=$model::select($model->getTable().".*")
			->join($this->class->getTable())->onColumn($this->class->getTable().".".$reference[1],$model->getTable().".".$model->primary_key);
		}else
		if($relation=="ManyToMany"){
			if(is_string($annotation)){
				$model_middle=new ($reference[1])();	
			}else{
				$model_middle=new (str_replace("/","\\",Config::filesystem('models.path'))."\\".$reference[1])();
			}
			//$attrib_middle=$model_middle->annotation_attributes[$reference[2]];
			$column_middle1=$reference[2];
			$column_middle2=$reference[3];
			$rs=$model::select($model->getTable().".*")
			->join($model_middle->getTable())->on($model_middle->getTable().".".$column_middle1,$value_id)
			->whereColumn($model->getTable().".".$model->primary_key,$model_middle->getTable().".".$column_middle2);
		}
		return compact('rs','relation');
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
			$callback=$value;
			if(is_array($column)){
				$type=$column;
			}
			$find=DB::table($model->table);
			$find->select();
			$callback($find);
			$rows=$find->execute()->fetchAll();
			if(count($rows)>0){
				if(is_array($type)){
					foreach($rows as $row){
						$model=new ($this->class)();
						$model->hidden=$this->hidden;
						$model->visible=$this->visible;
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
				if(is_array($type)){
					return [];
				}else{
					return null;
				}
			}
		}
		if($column==null){
			$column=$model->primary_key;
		}
		try{
			$rs=null;
			if($value==null){
				if(is_array($type)){
					return [];
				}else{
					return null;
				}
			}
			$rs=DB::table($model->table)->select()
			->where($column,$value)
			->execute();
			$rows=$rs->fetchAll();
			if($rs==null || sizeof($rows)<=0){
				if(is_array($type)){
					return [];
				}else{
					return null;
				}
			}
			$model->setValues($rows[0]);
			$class=[];
			foreach($rows as $row){
				$model=new ($this->class)();
				$model->hidden=$this->hidden;
				$model->visible=$this->visible;
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
		}
	}

	public function _all(){
		$model=$this;
		try{
			$rows=DB::table($model->table)->select()->execute()->fetchAll();
			if(count($rows)>0){
				foreach($rows as $row){
					$model=new ($this->class)();
					$model->hidden=$this->hidden;
					$model->visible=$this->visible;
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
		}
	}

	public function _create($row,$ignore_protected=null){
		$ignore_protected??=true;
		$model=$this;
		try{
			$model->setValues($row,$ignore_protected);
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
				}else
				if(is_array($value)){
					continue;
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