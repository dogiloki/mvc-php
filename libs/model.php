<?php

namespace libs;

use libs\Annotation;

class Model extends DB{

	protected $table;
	protected $class;
	protected $primary_key;
	protected $params;

	public function __construct($class){
		$this->class=$class;
		$reflection=new \ReflectionClass(get_class($this->class));
		$annotation=new Annotation($reflection->getDocComment());
		$this->table=$annotation->get("Table");
		//self::$params=get_class_vars(get_class($class));
		$this->getValues();
	}

	private function getValues(){
		foreach($this->class as $attrib=>$value){
			$this->params['attributes'][$attrib]=$value;
			$prop=null;
			try{
				$prop=new \ReflectionProperty(get_class($this->class),$attrib);
			}catch(\Exception $ex){
				continue;
			}
			$annotation=new Annotation($prop->getDocComment());
			$id=$annotation->get('ID');
			if($id!=null){
				$this->primary_key=$id;
				continue;
			}
			$column=$annotation->get('Column');
			if($column==null){
				continue;
			}
			$refert=$annotation->get('Reference');
			if($refert!=null){
				$attribute=explode(",",$refert)[1];
				$value=$value->$attribute??null;
			}
			$this->params['columns'][$column]=$value;
		}
	}

	public function setValues($row){
		if($row==null || sizeof($row)<=0){
			return;
		}
		foreach($this->class as $attrib=>$value){
			$value_original=$value;
			$prop=null;
			try{
				$prop=new \ReflectionProperty(get_class($this->class),$attrib);
			}catch(\Exception $ex){
				continue;
			}
			$annotation=new Annotation($prop->getDocComment());
			$id=$annotation->get('ID');
			$column=$annotation->get('Column');
			if($column==null && $id==null){
				continue;
			}
			$value=$row[$id??$column]??null;
			if($value!=null){
				unset($row[$id??$column]);
			}
			$refert=$annotation->get('Reference');
			if($refert!=null){
				$attribute="models\\".explode(",",$refert)[0];
				$value=$attribute::find($value);
			}
			$this->class->$attrib=$value??$value_original??null;
		}
		foreach($row as $column=>$value){
			if(!is_numeric($column)){
				$this->class->$column=$value;
			}
		}
	}

	public static function create($row){
		$self=self::class;
		$static=static::class;
		$model=new $self(new $static());
		$model->setValues($row);
		return $model->class;
	}

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
			$callback($find);
			$rs=$find->get();
			if($rs->rowCount()>0){
				$rows=$rs->fetchAll();
				$models=[];
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
			return null;
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
			return false;
		}
	}

}

?>