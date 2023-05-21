<?php

namespace libs\DB;

class Flat{

	public $value;

	public function __construct($value){
		$this->value=$value;
	}

	public static function __callStatic($name,$args){
		return new Flat($name."(".$args.")");
	}

}

?>