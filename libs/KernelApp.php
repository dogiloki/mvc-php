<?php

namespace libs;

class KernelApp{

	protected $services=[];

	public function __construct(){
		$this->services();
	}

	protected function services(){
		foreach($this->services as $service){
			$instance=new $service;
			try{
				$instance->handle();
				$instance->terminate();
			}catch(\Exception $ex){
				$instance->report($ex);
			}
		}
	}

}

?>