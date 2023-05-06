<?php

namespace libs;

use libs\Cofing;
use libs\Request;

require_once("Functions.php");

class Router{

	private $url=[];
	private $http_response_code=[];
	private $config=null;

	public function __construct(){
		$this->config=Config::singleton();
		
		/*foreach(glob('controllers/*.php') as $file){
			require_once $file;
		}*/
	}

	/*
	@param $url -> Ruta del enrutamiento
	@param $action -> Acción del controlador. Ejemplo: ClaseController@metodoController
	@param $private -> Necesita key en el encabezado
	@return Function con paramtro Request de tipo GET, POST y variables de url amigable
	*/
	public function get($url,$action=null,$private=false){
		$this->add('GET',$url,$action,$private);
	}
	public function post($url,$action=null,$private=false){
		$this->add('POST',$url,$action,$private);
	}
	public function put($url,$action=null,$private=false){
		$this->add('PUT',$url,$action,$private);
	}
	public function delete($url,$action=null,$private=false){
		$this->add('DELETE',$url,$action,$private);
	}
	public function error($code,$action=null){
		$this->http_response_code[$code]=$action;
	}
	public static $ext_views=["html","php"];
	public static function view($path,$params=[]){
		foreach(Router::$ext_views as $value){
			$require_path="views/".$path.".".$value;
			if(file_exists($require_path)){
				foreach($params as $key=>$param){
					$$key=$param;
				}
				require_once($require_path);
				return 0;
			}
		}
	}

	private function add($method,$url,$action=null,$private=false){
		$base_url=$this->splitUri($url);
		//echo "<pre>".print_r($base_url,"<br>")."</pre>";
		$this->url[]=[
			'method'=>$method,
			'url'=>$base_url['url'],
			'index_params'=>$base_url['index_params']??[],
			'params'=>$base_url['params']??[],
			'action'=>$action,
			'private'=>$private
		];
	}

	public function controller(){
		$base_url="/".trim(isset($_SERVER['REQUEST_URI'])?'/'.$_SERVER['REQUEST_URI'].'/':'/',"/");
		if($base_url!="/"){
			$base_url.="/";
		}
		//header("location:../".trim($base_url,"/"));
		$params=[];
		for($key=0; $key<sizeof($this->url); $key++){
			$base_url_new=explode("/",$base_url);
			foreach($this->url[$key]['index_params'] as $index){
				unset($base_url_new[$index+1]);
			}
			$base_url_new=implode("/",$base_url_new);
			if($base_url_new!=$this->url[$key]['url']){
				continue;
			}
			//echo $base_url_new."<br>";
			if($_SERVER['REQUEST_METHOD']!=$this->url[$key]['method']){
				continue;
			}
			if($this->url[$key]['private']){
				if(isset(getallheaders()['key'])){
					if(getallheaders()['key']!=$this->config->get('APP_KEY')){
						return $this->http_response_code(404);
					}
				}else{
					return $this->http_response_code(404);
				}
			}
			$urls=[];
			foreach($this->url[$key]['index_params'] as $index){
				$array_url=explode("/",$base_url);
				$urls[]=$array_url[$index+1];
			}
			$params=$this->url[$key]['params'];
			$count=0;
			/*echo "router: ".sizeof($params??[])."<br>";
			echo "url: ".(sizeof($urls));*/
			if(sizeof($params??[])!=(sizeof($urls))){
				return $this->http_response_code(404,$params);
			}
			$request=Request::singleton();
			// Agregar valor en las variables en la url
			foreach($params??[] as $param=>$value){
				$request->add('VAR',$param,$urls[$count]??"");
				$count++;
			}
			// Agregar request
			foreach($_REQUEST as $key_request=>$value_request){
				$request->add($_SERVER['REQUEST_METHOD'],$key_request,$value_request);
			}
			$action=$this->url[$key]['action'];
			$this->action($action,$request);
			return 0;
		}
		return $this->http_response_code(404,$params);
	}

	private function http_response_code($code,$params=[]){
		$request=Request::singleton();
		if($params==null || sizeof($params)<=0){
			// Agregar request
			foreach($_REQUEST as $key_request=>$value_request){
				$request->add($_SERVER['REQUEST_METHOD'],$key_request,$value_request);
			}
		}
		http_response_code($code);
		if($action=$this->http_response_code[$code]??null){
			$this->action($action,$request);
		}
	}

	private function action($action,$params=[]){
		if($action instanceof \Closure){
			echo $action($params);
		}else{
			$controller=explode('@',$action);
			$controller[0]="controllers\\".$controller[0];
			$obj=new $controller[0];
			echo $obj->{$controller[1]}($params);
		}
	}

	private function splitUri($uri){
		$uri=explode("/","/".trim($uri,"/"));
		if($uri[1]==""){
			$uri[1]="/";
		}
		return array_reduce($uri,function($array,$value)use($uri){
			if($value!=""){
				$letter_index=substr($value,0,1);
				$letter_end=substr($value,strlen($value)-1,strlen($value));
				if($letter_index=="{" && $letter_end=="}"){
					if(!isset($array['index_params'])){
						$array['index_params'][]=array_search($value,$uri)-1;
					}
					$value=substr($value,1,strlen($value)-2);
					$array['params'][$value]="";
				}else{
					if(!isset($array['url'])){
						$array['url']=$value=="/"?"":"/";
					}
					$array['url'].=$value.($value=="/"?"":"/");
				}
			}
			return $array;
		});
	}

}