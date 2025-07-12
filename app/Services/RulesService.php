<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;
use libs\Validator\Validator;

class RulesService implements ServiceImpl{

	public function handle(){
		Validator::make('required',function($key,$value,$array){
			return isset($array[$key]) && ($array[$key]!="" && $array[$key]!="null" && $array[$key]!=null);
		});
		Validator::make('nullable',function($key,$value,$array){
			return isset($array[$key]) && ($array[$key]=="" || $array[$key]=="null" || $array[$key]==null);
		});
		Validator::make('string',function($key,$value,$array){
			return is_string($value);
		});
		Validator::make('integer',function($key,$value,$array){
			return filter_var($value,FILTER_VALIDATE_INT);
		});
		Validator::make('decimal',function($key,$value,$array){
			return filter_var($value,FILTER_VALIDATE_FLOAT);
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
		Validator::make('url',function($key,$value,$array,$params){
			return filter_var($value,FILTER_VALIDATE_URL);
		});
		Validator::make('json',function($key,$value,$array,$params){
			json_decode($value??"");
			return json_last_error()===JSON_ERROR_NONE;
		});
		Validator::make('regex',function($key,$value,$array,$params){
			return preg_match($params[0],$value);
		});
		Validator::make('unique',function($key,$value,$array,$params){
			$model=$params[0];
			$field=$params[1];
			$exists=$model::where($field,$value)->first();
			return $exists==null;
		});
		Validator::make('exists',function($key,$value,$array,$params){
			$model=$params[0];
			$field=$params[1];
			$exists=$model::where($field,$value)->first();
			return $exists!=null;
		});
		Validator::make('same',function($key,$value,$array,$params){
			return $value==$array[$params[0]];
		});
		Validator::make('different',function($key,$value,$array,$params){
			return $value!=$array[$params[0]];
		});
		Validator::make('confirmed',function($key,$value,$array,$params){
			return $value==$array[$key.'_confirmation'];
		});
		Validator::make('ip',function($key,$value,$array,$params){
			return filter_var($value,FILTER_VALIDATE_IP);
		});
		Validator::make('mask',function($key,$value,$array,$params){
			$parts=explode('.',$value);
			if(count($parts)!=4) return false;
			$bin='';
			foreach($parts as $part){
				if(!is_numeric($part) || $part<0 || $part>255) return false;
				$bin.=str_pad(decbin($part),8,'0',STR_PAD_LEFT);
			}
			return preg_match('/^1*0*$/',$bin);
		});
		Validator::make('url',function($key,$value,$array,$params){
			return filter_var($value,FILTER_VALIDATE_URL);
		});
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>