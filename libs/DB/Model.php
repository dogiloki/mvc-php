<?php

namespace libs\DB;

use libs\Annotation;
use libs\DB\DB;

class Model{

	protected $table;
	protected $class;
	protected $primary_key;
	protected $params;
	protected $annotation_class;
	protected $annotation_attributes;
	protected $calls=[];

	public function __construct($class=null){
		$this->class=$class==null?$this:$class;
		$reflection=new \ReflectionClass(get_class($this->class));
		$annotation=new Annotation($reflection->getDocComment());
		$this->annotation_class=$annotation;
		$this->table=$annotation->get("Table");
		//self::$params=get_class_vars(get_class($class));
		$this->getValues();
	}

	public function getTable(){
		return $this->table;
	}

	private function getValues(){
		foreach($this->class as $attrib=>$value){
			if(property_exists(get_class($this->class),$attrib)){
				$visibility=(new \ReflectionProperty(get_class($this->class),$attrib))->isPublic();
				if(!$visibility){
					continue;
				}
			}
			$this->params['attributes'][$attrib]=$value;
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
				continue;
			}
			$column=$annotation->get('Column');
			if($column==null){
				continue;
			}
			$this->params['columns'][$column]=$value;
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
			if(($value==null && $relation==null)){
				unset($this->class->$attrib);
			}else{
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
		}
		// Llenar los atributos que no estan en la clase
		// foreach($row as $column=>$value){
		// 	if(!is_numeric($column)){
		// 		$this->class->$column=$value;
		// 	}
		// }
	}

	public function __get($attrib){
		return ($this->class->calls[$attrib])();
	}

	private function getReference($annotation,$reference,$value_id){
		$value=null;
		$model=new ("models\\".$reference[0])();
		//$attrib=$model->annotation_attributes[$reference[1]];
		$column=$reference[1];
		if($annotation->get('HasOne')!=null || $annotation->get('HasMany')!=null){
			$value=("models\\".$reference[0])::find($value_id,$column,($annotation->get('HasOne')?null:[]));
		}
		if($annotation->get('ManyToMany')!=null){
			$model_middle=new ("models\\".$reference[2])();
			//$attrib_middle=$model_middle->annotation_attributes[$reference[3]];
			$column_middle=$reference[3];
			$value=("models\\".$reference[0])::find(function($find)use($model,$model_middle,$column_middle,$value_id){
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

	public static function find($value,$column=null,$type=null){
		$self=self::class;
		$static=static::class;
		$model=new $self(new $static());
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
						$model=new $self(new $static());
						$model->setValues($row);
						$class[]=$model->class;
					}
					return $class;
				}else{
					$model->setValues($rows[0]);
					return $model->class;
				}
			}else{
				return [];
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
				$model=new $self(new $static());
				$model->setValues($row);
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

	public static function all(){
		$self=self::class;
		$static=static::class;
		$model=new $self(new $static());
		try{
			$rs=DB::table($model->table)->select()->execute();
			if($rs->rowCount()>0){
				$rows=$rs->fetchAll();
				foreach($rows as $row){
					$model=new $self(new $static());
					$model->setValues($row);
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

	public function save(){
		try{
			$this->getValues();
			$primary_key=$this->primary_key;
			$id=null;
			if(self::find($this->class->$primary_key)==null){
				DB::table($this->table)
				->insert($this->params['columns'])
				->execute();
			}else{
				DB::table($this->table)
				->update($this->params['columns'])
				->where($this->primary_key,$this->class->id)
				->execute();
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