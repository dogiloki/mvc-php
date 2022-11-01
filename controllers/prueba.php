<?php

require "models/prueba.php";

class ControllerPrueba{

    public $model;
	public $config;
	public $params=[];

	public function __construct(){
		$this->config=Config::singleton();
		$this->model=new ModelPrueba();
	}

	public function metodo($params){
		$params['json']=[
			"status"=>true,
			"data"=>$this->model->metodo()
		];
		view('json',$params);
	}

}



?>