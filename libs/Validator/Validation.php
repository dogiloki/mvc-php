<?php

namespace libs\Validator;

use libs\Validator\Rule;

class Validation{

	private $values;
	private $errors;

	public function __construct($values,$errors){
		$this->values=$values;
		$this->errors=$errors;
	}

	public function values(){
		return $this->values;
	}

	public function get($key){
		return $this->values[$key]??null;
	}

	public function set($key,$value){
		$this->values[$key]=$value;
		return $this;
	}

	public function errors(){
		return $this->errors;
	}

	public function fails(){
		return !empty($this->errors());
	}

}

?>