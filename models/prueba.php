<?php

class ModelPrueba{

    protected $db;

	public function __construct(){
		// Instancia única de PDO
		$this->db=DB::singleton();
	}

	public function metodo(){
		return "Esto es un resultado de prueba";
	}

}

?>