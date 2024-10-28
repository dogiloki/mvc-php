<?php

namespace database\seeders;

use libs\DB\Seeder;
use libs\Middle\Secure;
use app\Models\User;

class UserSeeder extends Seeder{

	public function run(){
		User::create([
			'name'=>'Julio Cesar',
			'surname1'=>'Villanueva',
			'surname2'=>'Ontiveros',
			'registration'=>'D-6753644',
			'password'=>Secure::encodePassword('123')
		]);
		$registers=["A","C","D"];
		for($index=0; $index<1000; $index++){
			User::create([
				'name'=>$this->faker->firstName,
				'surname1'=>$this->faker->lastName,
				'surname2'=>$this->faker->lastName,
				'registration'=>$registers[array_rand($registers)]."-".mt_rand(100000,999999),
				'password'=>Secure::encodePassword('123')
			]);
		}
	}

}

?>