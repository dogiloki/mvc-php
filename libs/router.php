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
	public const EXT_VIEWS=["html","php"];
	public static function view($path,$params=[]){
		foreach(Router::EXT_VIEWS as $value){
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
		$base_url=$this->separarCadena($url);
		$this->url[]=[
			'method'=>$method,
			'url'=>$base_url['url'],
			'index_param'=>$base_url['index_param'],
			'params'=>$base_url['params'],
			'action'=>$action,
			'private'=>$private
		];
	}

	public function controller(){
		$base_url="/".trim(isset($_GET['url'])?'/'.$_GET['url'].'/':'/',"/");
		//echo trim($base_url,"/");
		//header("location:../".trim($base_url,"/"));
		$params=[];
		for($key=0; $key<sizeof($this->url); $key++){
			if(implode('/',array_slice(explode('/',$base_url),0,$this->url[$key]['index_param']))."/"!=$this->url[$key]['url']){
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
			$request=new Request();
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
		$request=new Request();
		if($params==null || sizeof($params)<=0){
			// Agregar request
			foreach($_REQUEST as $key_request=>$value_request){
				$request->add($_SERVER['REQUEST_METHOD'],$key_request,$value_request);
			}
		}
		if($action=$this->http_response_code[$code]??null){
			$this->action($action,$request);
		}
		http_response_code($code);
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

	private function separarCadena($cadena){
		$cadena="/".trim($cadena."/");
		$separador = "/{";
	    if($cadena == "/"){
	        $estado = 0;
	        $cade_separada = "/";
	    }else{
	        $estado = 1;
	        $cade_separada = explode($separador, $cadena);
	    }
    	return $this->guardarValores($cade_separada, $estado);
	}

	private function guardarValores($cadena_separada, $estado){
	    $parametros = null;
	    if($estado == 0){
	        $url = $cadena_separada;
	    }else{
	        $url = $cadena_separada[0];
	        while((strpos($url,"//")) !== false){
	            $url = str_replace("//", "/", $url);
	        }
	        if(count($cadena_separada) > 1){
	            $parametros = $this->eliminarCorchetes($cadena_separada);
	        }
	    }
	    $url="/".trim($url,"/")."/";
		return[
	    	'url'=>$url,
	    	'index_param'=>sizeof(explode('/',$url))-1,
	    	'params'=>$parametros,
	    ];
	}

	private function eliminarCorchetes($cadena_separada){
	    for($i=1; $i < count($cadena_separada); $i++){
	        $trimmed = trim($cadena_separada[$i], "/");
	        $trimmed = trim($trimmed, "{");
	        $trimmed = trim($trimmed, "}");
	        $parametros[$trimmed] = "";
    	}
    	return $parametros;
	}

}

class Request{

	private $var=[];
	private $get=[];
	private $post=[];

	public function __construct(){

	}

	public function add($type,$key,$value){
		switch($type){
			case 'VAR': $this->var[$key]=$value; break;
			case 'GET': $this->get[$key]=$value; break;
			case 'POST': $this->post[$key]=$value; break;
		}
	}

	public function get($key){
		return $this->get[$key]??null;
	}

	public function post($key){
		return $this->post[$key]??null;
	}

	public function var($key){
		return $this->var[$key]??null;
	}

}

?>