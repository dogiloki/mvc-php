<?php

namespace libs\Validator;

use libs\Middle\Singleton;
use libs\Validator\Rule;

class Validator extends Singleton{

	private $rules=[];

	public function _make($key,$action){
		$this->rules[$key]=new Rule($key,$action);
	}

	public function _rules(){
		return $this->rules;
	}

}

?>