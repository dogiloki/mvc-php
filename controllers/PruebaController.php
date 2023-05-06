<?php

namespace controllers;

use models\Prueba;

class PruebaController{

	public $model;

	public function __construct(){
		$this->model=new Prueba();
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