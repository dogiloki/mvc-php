<?php

namespace libs\Middle;

class Faker{

    public $faker;

    public function __construct(){
        $this->faker=\Faker\Factory::create(config()->app('faker_locale'));
    }

    public function __get($name){
        return $this->faker->$name;
    }

    public function __call($name,$args){
        return $this->faker->$name(...$args);
    }

}

?>