<?php

namespace libs;

class Request{

	private static $instance=null;
	public $header=null;

	private function __construct(){
		$this->get=[];
		$this->post=[];
		$this->put=[];
		$this->cookie=$_COOKIE;
		$this->session=$_SESSION??[];
		$this->files=$_FILES??[];
		$this->header=getallheaders();
	}

	public static function singleton(){
		if(self::$instance==null){
			self::$instance=new Request();
		}
		return self::$instance;
	}

	public function add($type,$key,$value){
		$this->$key=$value;
		switch($type){
			case 'GET': $this->get[$key]=$value; break;
			case 'POST': $this->post[$key]=$value; break;
			case 'PUT': $this->put[$key]=$value; break;
		}
	}

	public function cookie($key){
		return $this->cookie[$key]??null;
	}

	public function session($key){
		return $this->session[$key]??null;
	}

	public function files($key){
		return $this->files[$key]??null;
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

	public function put($key=null){
		if($key==null){
			return $this->put;
		}
		return $this->put[$key]??null;
	}

}

?>