<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;

class ReportService implements ServiceImpl{

	public function handle(){
		if(config()->app('debug')){
			error_reporting(E_ALL);
			ini_set('display_errors',1);
		}
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