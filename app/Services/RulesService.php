<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;
use libs\Validator\Validator;

class RulesService implements ServiceImpl{

	public function handle(){
		Validator::make('required',function($key,$value,$array){
			return isset($array[$key]);
		});
		Validator::make('string',function($key,$value,$array){
			return is_string($value);
		});
		Validator::make('integer',function($key,$value,$array){
			return is_integer($value);
		});
		Validator::make('decimal',function($key,$value,$array){
			return is_double($value);
		});
		Validator::make('number',function($key,$value,$array){
			return is_numeric($value);
		});
		Validator::make('array',function($key,$value,$array){
			return is_array($value);
		});
		Validator::make('boolena',function($key,$value,$array){
			return is_bool($value);
		});
		Validator::make('date',function($key,$value,$array){
			return strtotime($value)!==false;
		});
		Validator::make('min',function($key,$value,$array,$params){
			return $value<=$params[0];
		});
		Validator::make('max',function($key,$value,$array,$params){
			return $value>=$params[0];
		});
		Validator::make('between',function($key,$value,$array,$params){
			return $value>=$params[0] && $value<=$params[1];
		});
		Validator::make('in',function($key,$value,$array,$params){
			return $value==$params[0];
		});
		Validator::make('email',function($key,$value,$array,$params){
			return filter_var($value,FILTER_VALIDATE_EMAIL);
		});
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>