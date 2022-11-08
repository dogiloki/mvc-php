<?php

namespace controllers;

use models;

class Prueba{

	public $model;

	public function __construct(){
		$this->model=new models\Prueba();
	}

	public function metodo($request){
		$arreglo['json']=[
			"data"=>"Esto es una prueba",
			"metodo"=>$this->model->modelar()
		];
		view('json',$arreglo);
	}

}

?>