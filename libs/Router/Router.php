<?php

namespace libs\Router;

use libs\HTTP\Request;
use libs\Config;

class Router{

	private static $instance=null;
	private $routes=[];
	private $prev_route=null;

	private function __construct(){
		/*foreach(glob('controllers/*.php') as $file){
			require_once $file;
		}*/
	}
	
	public static function singleton(){
		if(self::$instance==null){
			self::$instance=new Router();
		}
		return self::$instance;
	}

	public function getRoutes(){
		return $this->routes;
	}

	/*
	@param $uri -> Ruta del enrutamiento
	@param $action -> Acción del controlador. Ejemplo: ClaseController@metodoController
	@param $private -> Necesita key en el encabezado
	@return Function con paramtro Request de tipo GET, POST y variables de uri amigable
	*/
	public function get($uri,$action=null,$private=false){
		return $this->add('GET',$uri,$action,$private);
	}
	public function post($uri,$action=null,$private=false){
		return $this->add('POST',$uri,$action,$private);
	}
	public function put($uri,$action=null,$private=false){
		return $this->add('PUT',$uri,$action,$private);
	}
	public function delete($uri,$action=null,$private=false){
		return $this->add('DELETE',$uri,$action,$private);
	}

	private function add($method,$uri,$action=null,$private=false){
		$name_file=explode(".",basename(debug_backtrace()[1]['file']))[0];
		if($name_file=="api"){
			$uri="api/".$uri;
		}
		$route=new Route($method,$uri,$action);
		$route->name_file=$name_file;
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
			foreach(json_decode(file_get_contents('php://input')??null,true)??[] as $key_request=>$value_request){
				$request->add($_SERVER['REQUEST_METHOD'],$key_request,$value_request);
				$request->{$key_request}=$value_request;
			}
			$route->middlewares=array_merge((array)Config::middleware('routers.'.$route->name_file)??[],$route->middlewares);
			$route->call($request);
			return;
		}
		return abort(404);
	}

}