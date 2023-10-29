<?php

namespace libs\Validator;

class Rule{

	protected $key=null;
	protected $action=null;

	public function __construct($key=null,$action=null){
		$this->key??=$key??strtolower(get_class($this));
		$this->action=$action;
	}

	public function passes($key,$value,$array,$params){
		return ($this->action)($key,$value,$array,$params);
	}

	public function message(){
		return __("validation.".$this->key);
	}

}

?>