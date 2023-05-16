<?php

namespace libs\Router;

use libs\Router\Route;
use libs\Router\Request;
use libs\Router\View;

class Router{

	private static $instance=null;
	private $routes=[];
	private $prev_route=null;
	private $http_response_code=[];

	private function __construct(){
		/*foreach(glob('controllers/*.php') as $file){
			require_once $file;
		}*/
	}
	
	public static function singletong(){
		if(self::$instance==null){
			self::$instance=new Router();
		}
		return self::$instance;
	}

	public function getRoutes(){
		return $this->routes;
	}

	/*
	@param $url -> Ruta del enrutamiento
	@param $action -> Acción del controlador. Ejemplo: ClaseController@metodoController
	@param $private -> Necesita key en el encabezado
	@return Function con paramtro Request de tipo GET, POST y variables de url amigable
	*/
	public function get($url,$action=null,$private=false){
		return $this->add('GET',$url,$action,$private);
	}
	public function post($url,$action=null,$private=false){
		return $this->add('POST',$url,$action,$private);
	}
	public function put($url,$action=null,$private=false){
		return $this->add('PUT',$url,$action,$private);
	}
	public function delete($url,$action=null,$private=false){
		return $this->add('DELETE',$url,$action,$private);
	}
	public function error($code,$action=null){
		return $this->http_response_code[$code]=$action;
	}

	public static $ext_views=["html","php"];
	public static function view($path,$params=[]){
		$path=str_replace(".","/",$path);
		foreach(Router::$ext_views as $value){
			$require_path="views/".$path.".".$value;
			if(file_exists($require_path)){
				foreach($params as $key=>$param){
					$$key=$param;
				}
				eval("?>".View::render($require_path)."<?php");
				return 0;
			}
		}
	}

	private function add($method,$url,$action=null,$private=false){
		$route=new Route($method,$url,$action);
		$this->prev_route=$route;
		$this->routes[]=$route;
		return $this;
	}

	public function name(string $name){
		$this->prev_route->name=$name;
		return $this;
	}

	public function middleware(...$action){
		$this->prev_route->middlewares=$action;
		return $this;
	}

	public function controller(){
		$base_uri=explode("/",Route::formatUri($_SERVER['REQUEST_URI']));
		$params=[];
		$request=Request::singleton();
		foreach($this->routes as $route_index=>$route){
			if($route->method!=$_SERVER['REQUEST_METHOD']){
				continue;
			}
			$params=[];
			$request=Request::clear();
			$count_uri_found=0;
			$route_uri=explode("/",$route->path);
			foreach($route_uri as $path_index=>$path){
				$uri=($base_uri[$path_index]??null);
				$param=($route->params[$path_index]??null);
				if($path!=$uri && $param==null){
					continue;
				}
				if($param!=null && $param['optional']==false && $uri==null){
					continue;
				}
				if($param!=null){
					$params[$param['name']]=$uri;
					$request->{$param['name']}=$uri;
					$request->add('VAR',$param['name'],$uri);
				}
				$count_uri_found++;
			}
			if($count_uri_found!=count($route_uri)){
				continue;
			}
			foreach($_REQUEST as $key_request=>$value_request){
				$request->add($_SERVER['REQUEST_METHOD'],$key_request,$value_request);
				$request->{$key_request}=$value_request;
			}
			$route->call($request);
			return;
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

}