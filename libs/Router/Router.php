<?php

namespace libs\Router;

use libs\HTTP\Request;
use libs\Config;
use libs\Middleware\Middleware;

class Router{

	private static $instance=null;
	private $routes=[];
	private $route=null;
	private $global_route=null;
	private $assign_globally=true;

	private function __construct(){
		$this->route=new Route();
		$this->global_route=new Route();
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

	private function applyParams(){
		$this->route->group=$this->global_route->group;
		$this->route->name=$this->global_route->name;
		$this->route->middlewares=array_merge($this->route->middlewares,$this->global_route->middlewares);
	}

	private function add($method,$uri,$action=null,$private=false){
		$route=new Route($method,$uri,$action);
		$this->route=$route;
		$this->applyParams();
		$this->routes[]=$route;
		return $this;
	}

	// Configurar route

	public function group($action){
		if($action instanceof \Closure){
			$this->assign_globally=false;
			$action();
			$this->global_route=new Route();
			$this->assign_globally=true;
		}else{
			$this->route->group=$action;
			if($this->assign_globally){
				$this->global_route->group=$action;
			}
			return $this;
		}
	}

	public function name(string $name){
		$this->route->name=$name;
		if($this->assign_globally){
			$this->global_route->name=$name;
		}
		return $this;
	}

	public function middleware(...$action){
		$this->route->middlewares=array_merge($this->route->middlewares,$action);
		if($this->assign_globally){
			$this->global_route->middlewares=array_merge($this->global_route->middlewares,$action);
		}
		return $this;
	}

	// Iniciar enrutamiento

	public function controller(){
		$base_uri=explode("/",explode("?",Route::formatUri($_SERVER['REQUEST_URI']))[0]);
		$params=[];
		$request=Request::singleton();
		foreach($this->routes as $route){
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
			$route->middlewares=array_merge(Middleware::middleware(),$route->middlewares);
			$route->call($request);
			return;
		}
		return abort(404);
	}

}