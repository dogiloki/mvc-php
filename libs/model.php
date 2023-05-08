<?php

namespace libs;

use libs\Annotation;

class Model extends DB{

	protected $table;
	protected $class;
	protected $primary_key;
	protected $params;
	protected $annotation_class;
	protected $annotation_attributes;

	public function __construct($class=null){
		$this->class=$class==null?$this:$class;
		$reflection=new \ReflectionClass(get_class($this->class));
		$annotation=new Annotation($reflection->getDocComment());
		$this->annotation_class=$annotation;
		$this->table=$annotation->get("Table");
		//self::$params=get_class_vars(get_class($class));
		$this->getValues();
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

	private function setValues($row,$ignore_relation=false){
		if($row==null || sizeof($row)<=0){
			return;
		}
		$value_id=null;
		foreach($this->class as $attrib=>$value){
			$value_original=$value;
			$annotation=$this->annotation_attributes[$attrib]??null;
			if($annotation==null){
				continue;
			}
			$id=$annotation->get('ID');
			$column=$annotation->get('Column');
			$relation=$annotation->get('OneToOne')??$annotation->get('OneToMany')??$annotation->get('ManyToOne')??$annotation->get('ManyToMany');
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
				unset($row[$id??$column]);
				if($relation!=null){
					$reference=explode(',',$relation);
					$model=new ("models\\".$reference[0])();
					$model_attrib=$model->annotation_attributes[$reference[1]];
					$model_column=$model_attrib->get('ID')??$column->get('Column');
					if($ignore_relation){
						$this->class->{'_'.$attrib}=fn()=>$this->getReference($annotation,$reference[0],$value_id,$model_column);
						unset($this->class->$attrib);
						break;
					}else{
						$value=$this->getReference($annotation,$reference[0],$value_id,$model_column);
					}
				}
				$this->class->$attrib=$value??$value_original??null;
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
		$attrib='_'.$attrib;
		return ($this->class->$attrib)();
	}

	private function getReference($annotation,$model,$column,$value){
		if($annotation->get('OneToOne')!=null){
			$value=("models\\".$model)::find($column,$value,null,true);
		}
		if($annotation->get('OneToMany')!=null){
			$value=("models\\".$model)::find($column,$value,[],true);
		}
		return $value;
	}

	public static function create($row){
		$self=self::class;
		$static=static::class;
		$model=new $self(new $static());
		$model->setValues($row);
		return $model->class;
	}

	public static function find($value,$column=null,$type=null,$ignore_relation=false){
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
						$model->setValues($row,$ignore_relation);
						$class[]=$model->class;
					}
					return $class;
				}else{
					$model->setValues($rows[0],$ignore_relation);
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
			$model->setValues($rows[0],$ignore_relation);
			$class=[];
			foreach($rows as $row){
				$model=new $self(new $static());
				$model->setValues($row,$ignore_relation);
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

	public function update($row){
		unset($row[$this->primary_key]);
		$this->setValues($row);
		return $this->class;
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