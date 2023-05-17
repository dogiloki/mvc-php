<?php

namespace libs\DB;

use libs\Middle\Faker;

class Seeder extends Faker{

	public function run(){}

	public function call($calls=[]){
		foreach($calls as $call){
			$seeder=new $call();
			$seeder->run();
		}
	}

}

?>