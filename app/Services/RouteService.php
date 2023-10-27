<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;
use libs\Router\Route;

class RouteService implements ServiceImpl{

	public function handle(){
		
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>