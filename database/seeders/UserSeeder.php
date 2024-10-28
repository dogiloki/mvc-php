<?php

namespace database\seeders;

use libs\DB\Seeder;
use libs\Middle\Secure;
use app\Models\User;

class UserSeeder extends Seeder{

	public function run(){
		for($index=0; $index<10; $index++){
			User::create([
				'name'=>$this->faker->name,
				'email'=>$this->faker->email,
				'password'=>Secure::encodePassword('123')
			]);
		}
	}

}

?>