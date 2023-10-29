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
				$rule_class=(__NAMESPACE__)."\\Rules\\".(str_replace(' ','',ucwords(str_replace('_',' ',$rule))));
				if(class_exists($rule_class)){
					$action=new $rule_class($rule);
				}else{
					$action=Validator::rules()[$rule]??null;
				}
				$params=isset($rule_split[1])?explode(",",$rule_split[1]):[];
				if($action===null){
					continue;
				}
				if(!$action->passes($key,$value,$values,$params)){
					$errors[$key][]=messageFormat($action->message(),array_merge(['key'=>__("attributes.".$key)],$params),":");
				}
			}
		}
		return new Validation($values,$errors);
	}

}

?>