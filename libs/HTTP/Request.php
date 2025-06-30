<?php

namespace libs\HTTP;

use libs\Session\Session;
use libs\Cookie\Cookie;
use libs\Middle\Secure;

class Request{

	private static $instance=null;
	private $header;
	private $get;
	private $post;
	private $put;
	private $input;
	private $cookie;
	private $session;
	private $files;

	private function __construct(){
		$this->get=[];
		$this->post=[];
		$this->put=[];
		$this->input=[];
		$this->cookie=[];
		$this->session=Session::singleton();
		$this->files=$_FILES??[];
		$this->header=getallheaders();
	}

	public static function singleton(){
		if(self::$instance==null){
			self::$instance=new Request();
		}
		return self::$instance;
	}

	public static function clear(){
		self::$instance=null;
		return self::singleton();
	}

	public function add($type,$key,$value){
		$this->$key=$value;
		$this->input[$key]=$value;
		switch($type){
			case 'GET': $this->get[$key]=$value; break;
			case 'POST': $this->post[$key]=$value; break;
			case 'PUT': $this->put[$key]=$value; break;
		}
	}

	public function cookie(){
		return new Cookie();
	}

	public function session(){
		return Session::singleton();
	}

	public function method(){
		return $_SERVER['REQUEST_METHOD']??null;
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

	public function get($key=null, $value=null){
		if($key==null){
			return $this->get;
		}
		if($value==null){
			return $this->get[$key]??null;
		}
		return $this->get[$key]=$value;
	}

	public function post($key=null, $value=null){
		if($key==null){
			return $this->post;
		}
		if($value==null){
			return $this->post[$key]??null;
		}
		return $this->post[$key]=$value;
	}

	public function put($key=null, $value=null){
		if($key==null){
			return $this->put;
		}
		if($value==null){
			return $this->put[$key]??null;
		}
		return $this->put[$key]=$value;
	}

	public function input($key=null, $value=null){
		if($key==null){
			return $this->input;
		}
		if($value==null){
			return $this->input[$key]??null;
		}
		return $this->input[$key]=$value;
	}

	public function all(){
		return array_merge($_REQUEST,$this->input());
	}

	public function only($class){
		$model=new $class();
		return array_intersect_key($this->all(),$model->getFillableArray());
	}

	public function bearerToken(){
		$authorization=$this->header('Authorization');
		if($authorization){
			$authorization=explode(' ',$authorization);
			if(count($authorization)==2){
				if($authorization[0]=='Bearer'){
					return $authorization[1];
				}
			}
		}
		return null;
	}

	public function path(){
		return $_SERVER['REQUEST_URI'];
	}

	public static function ip(){
		return $_SERVER['REMOTE_ADDR'];
	}

	public static function userAgent(){
		return $_SERVER['HTTP_USER_AGENT'];
	}

}

?>