<?php

namespace libs\Validator;

use libs\Middle\Singleton;

class Validator extends Singleton{

	private $rules=[];

	public function _make($key,$action){
		$this->rules[$key]=$action;
	}

	public function _rules(){
		return $this->rules;
	}

}

?>