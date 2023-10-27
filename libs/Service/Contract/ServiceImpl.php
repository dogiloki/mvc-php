<?php

namespace libs\Service\Contract;

interface ServiceImpl{

	public function handle();
	public function terminate();
	public function report($ex);
	
}

?>