<?php

namespace libs;

class Annotation{

	public $params=[];
	public $text;

	public function __construct($text){
		$this->text=$text;
		$index_column=null;
		$index_value=null;
		$count_column=null;
		$count_value=null;
		foreach(str_split($text) as $row=>$letter){
			if($letter=="@"){
				$index_column=$row+1;
			}
			if($index_column==null){
				continue;
			}
			if($letter!="(" && $index_value==null){
				$count_column++;
			}else{
				if($count_value==null){
					$index_value=$row+1;
				}
			}
			if($index_value==null){
				continue;
			}
			if($letter!=")"){
				$count_value++;
				continue;
			}
			$this->params[substr($text,$index_column,$count_column-1)]=substr($text,$index_value,$count_value-1);
			$index_column=null;
			$index_value=null;
			$count_column=null;
			$count_value=null;
		}
	}

	public function get($key){
		return $this->params[$key]??null;
	}

}

?>