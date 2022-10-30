<?php

require_once "models/prueba.php";

class ControllerPrueba{

    public $model;
	public $config;
	public $json=[];

	public function __construct(){
		$this->config=Config::singleton();
		$this->model=new ModelPrueba();
	}

	public function metodo($params){
		$this->json=[
			"status"=>true,
			"data"=>$this->model->metodo()
		];
		$this->render();
	}

	public function render(){
		require 'views/json.php';
	}

}



?>