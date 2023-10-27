<?php

namespace libs\Middleware;

use libs\Middle\Singleton;

class Middleware extends Singleton{

	private $middlewares=[];
	private $middlewares_group=[];
	private $middlewares_alias=[];

	protected function __construct(){

	}

	public function _middleware($middleware=null){
		return ($middleware===null)?($this->middlewares):(($middleware!==null && is_array($middleware))?($this->middlewares=$middleware):($this->middlewares[]=$middleware));
	}

	public function _middlewareGroup($group,$middleware=null){
		
	}

	public function _middlewareAlias($alias=null,$middleware=null){
		return is_array($alias)?$this->middlewares_alias=$alias:$this->middlewares_alias[$alias]=$middleware;
	}

	public function _alias($alias){
		return $this->middlewares_alias[$alias];
	}

}

?>