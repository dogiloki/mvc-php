<?php

namespace libs\Validator;


class Validation{

	private $errors;
	private $values;

	public function __construct($values,$errors){
		$this->values=$values;
		$this->errors=$errors;
	}

	public function values(){
		return $this->values;
	}

	public function errors(){
		return $this->errors;
	}

	public function fails(){
		return count($this->errors)==0;
	}

	public function has($key){
		return isset($this->errors[$key]);
	}

}

?>