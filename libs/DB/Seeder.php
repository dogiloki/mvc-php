<?php

namespace libs\DB;

use libs\Middle\Faker;
use libs\Console\Console;

class Seeder extends Faker{

	public function run(){}

	public function call($calls=[]){
		$console=new Console();
		foreach($calls as $call){
			$seeder=new $call();
			$console->info($call." ".$seeder->run());
		}
	}

}

?>