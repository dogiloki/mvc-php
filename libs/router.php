<?php

class Router{

	private $url=[];
	public $config=null;

	public function __construct(){
		$this->config=Config::singleton();
		foreach(glob('controllers/*.php') as $file){
			require $file;
		}
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
	public function delte($url,$action=null,$private=false){
		$this->add('DELETE',$url,$action,$private);
	}
	public const EXT_VIEWS=["html","php"];
	public static function view($path){
		foreach(Router::EXT_VIEWS as $value){
			$require_path="views/".$path.".".$value;
			if(file_exists($require_path)){
				require $require_path;
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
		$base_url="/".trim(isset($_GET['url'])?'/'.$_GET['url']:'/',"/")."/";
		//echo trim($base_url,"/");
		//header("location:../".trim($base_url,"/"));
		for($key=0; $key<sizeof($this->url); $key++){
			if(implode('/',array_slice(explode('/',$base_url),0,$this->url[$key]['index_param']))."/"!=$this->url[$key]['url']){
				continue;
			}
			if($_SERVER['REQUEST_METHOD']!=$this->url[$key]['method']){
				return http_response_code(404);
			}
			if($this->url[$key]['private']){
				if(isset(getallheaders()['key'])){
					if(getallheaders()['key']!=$this->config->get('key')){
						return http_response_code(404);
					}
				}else{
					return http_response_code(404);
				}
			}
			$urls=array_slice(explode("/",$base_url),$this->url[$key]['index_param']);
			$params=$this->url[$key]['params'];
			$count=0;
			/*echo "router: ".sizeof($params??[])."<br>";
			echo "url: ".(sizeof($urls)-1);*/
			if(sizeof($params??[])!=(sizeof($urls)-1)){
				return http_response_code(404);
			}
			// Agregar valor en las variables en la url
			foreach($params??[] as $param=>$value){
				$params[$param]=$urls[$count]??"";
				$count++;
			}
			// Agregar request NO USE KEY 'url'
			foreach($_REQUEST as $key_request=>$value_request){
				$params[$key_request]=$value_request;
			}
			$action=$this->url[$key]['action'];
			function view($path){
				Router::view($path);
			}
			$this->action($action,$params);
			return 0;
		}
		return http_response_code(404);
	}

	private function action($action,$params){
		if($action instanceof \Closure){
			$action($params);
		}else{
			$controller=explode('@',$action);
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

?>