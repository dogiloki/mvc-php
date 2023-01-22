<?php

use libs\Config;

spl_autoload_register(function($clase){
	$path=str_replace("\\","/",$clase).".php";
	//echo $path."<br>";
	if(file_exists($path)){
		require_once $path;
	}
});

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
				require_once $require_path;
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
			'index_param'=>$base_url['index_param']??sizeof(explode($base_url['url'],"/"))+2,
			'params'=>$base_url['params']??[],
			'action'=>$action,
			'private'=>$private
		];
	}

	public function controller(){
		$base_url="/".trim(isset($_GET['url'])?'/'.$_GET['url'].'/':'/',"/");
		//echo trim($base_url);
		//header("location:../".trim($base_url,"/"));
		$params=[];
		for($key=0; $key<sizeof($this->url); $key++){
			if(implode('/',array_slice(explode('/',$base_url),0,$this->url[$key]['index_param'])).($base_url=="/"?"":"/")!=$this->url[$key]['url']){
				continue;
			}
			if($_SERVER['REQUEST_METHOD']!=$this->url[$key]['method']){
				return $this->http_response_code(404);
			}
			if($this->url[$key]['private']){
				if(isset(getallheaders()['key'])){
					if(getallheaders()['key']!=$this->config->get('key')){
						return $this->http_response_code(404);
					}
				}else{
					return $this->http_response_code(404);
				}
			}
			$urls=array_slice(explode("/",$base_url),$this->url[$key]['index_param']);
			$params=$this->url[$key]['params'];
			$count=0;
			/*echo "router: ".sizeof($params??[])."<br>";
			echo "url: ".(sizeof($urls));*/
			if(sizeof($params??[])!=(sizeof($urls))){
				return $this->http_response_code(404,$params);
			}
			$request=new Request(getallheaders());
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
		$request=new Request(getallheaders());
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
		function view($path,$params=[]){
			Router::view($path,$params);
		}
		function config($key){
			echo Config::singleton()->get($key);
		}
		if($action instanceof \Closure){
			$action($params);
		}else{
			$controller=explode('@',$action);
			$controller[0]="controllers\\".$controller[0];
			$obj=new $controller[0];
			$obj->{$controller[1]}($params);
		}
	}

	private function splitUri($uri){
		$uri=explode("/","/".trim($uri,"/"));
		if($uri[1]==""){
			$uri[1]="/";
		}
		$conta=0;
		return array_reduce($uri,function($array,$value) use($conta){
			if($value!=""){
				$letter_index=substr($value,0,1);
				$letter_end=substr($value,strlen($value)-1,strlen($value));
				$conta++;
				if($letter_index=="{" && $letter_end=="}"){
					if(!isset($array['index_param'])){
						$array['index_param']=$conta+2;
					}
					$value=substr($value,1,strlen($value)-2);
					$array['params'][$value]="sa";
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

class Request{

	private $var=[];
	private $get=[];
	private $post=[];
	private $header=null;

	public function __construct($header){
		$this->header=$header;
	}

	public function add($type,$key,$value){
		switch($type){
			case 'VAR': $this->var[$key]=$value; break;
			case 'GET': $this->get[$key]=$value; break;
			case 'POST': $this->post[$key]=$value; break;
		}
	}

	public function header($key=null){
		if($key==null){
			return $this->header;
		}
		return $this->header[$key]??null;
	}

	public function get($key=null){
		if($key==null){
			return $this->get;
		}
		return $this->get[$key]??null;
	}

	public function post($key=null){
		if($key==null){
			return $this->post;
		}
		return $this->post[$key]??null;
	}

	public function var($key=null){
		if($key==null){
			return $this->var;
		}
		return $this->var[$key]??null;
	}

}

?>