<?php

namespace libs\Middle;

require_once __DIR__ . '/../../vendor/autoload.php';

class Faker{

    public $faker;

    public function __construct(){
        $this->faker=\Faker\Factory::create();
    }

    public function __get($name){
        return $this->faker->$name;
    }

    public function __call($name,$args){
        return $this->faker->$name(...$args);
    }

}

?>