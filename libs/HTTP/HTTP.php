<?php

namespace libs\HTTP;

require_once('vendor/autoload.php');

class HTTP{

	public static $host;

	public static function post($uri,$action,$params=[]){
		return self::request('POST',$uri,$action,$params);
	}

	public static function get($uri,$action,$params=[]){
		return self::request('GET',$uri,$action,$params);
	}

	public static function put($uri,$action,$params=[]){
		return self::request('PUT',$uri,$action,$params);
	}

	public static function delete($uri,$action,$params=[]){
		return self::request('DELETE',$uri,$action,$params);
	}

	public static function request($method,$uri,$action,$params=[]){
		$client=new \GuzzleHttp\Client(['verify'=>false]);
		$data=[
			'headers'=>[
				"content-type"=>"application/json",
				"accept"=>"application/vnd.com.payclip.v2+json",
				"accept"=>"application/vnd.com.payclip.v1+json"
			]
		];
		if($method!='GET'){
			$data['body']=json_encode($params);
		}
		$response=null;
		try{
			$response=$client->request($method,self::$host.$uri,$data);
		}catch(\Exception $ex){
			
			return $action(null);
		}
		return $action(json_decode($response->getBody()??[]));
	}

}

?>