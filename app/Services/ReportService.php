<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;

class ReportService implements ServiceImpl{

	public function handle(){
		error_reporting(E_ALL | E_DEPRECATED);
		ini_set('display_errors',config()->app('debug')?1:0);
		set_exception_handler("exception");
		set_error_handler("error");
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>