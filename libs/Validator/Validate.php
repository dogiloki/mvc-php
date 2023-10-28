<?php

namespace libs\Validator;

use libs\Validator\Validator;
use libs\Validator\Validation;

class Validate{

	public static function make($values,$array_rules){
		$errors=[];
		foreach($array_rules as $key=>$rules){
			$value=$values[$key]??null;
			$rules=explode("|",$rules);
			foreach($rules as $rule){
				$rule_split=explode(":",$rule);
				$rule=$rule_split[0];
				$action=Validator::rules()[$rule]??null;
				if($action===null){
					continue;
				}
				$params=isset($rule_split[1])?explode(",",$rule_split[1]):[];
				if(!$action($key,$value,$values,$params)){
					$errors[$key][]=__("validation.".$rule,array_merge(['key'=>__("attributes.".$key)],$params));
				}
			}
		}
		return new Validation($values,$errors);
	}

}

?>