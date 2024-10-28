<?php

namespace app\Validators;

use libs\Validator\Validate;

class UserValidator{

    public static function store($array){
        return Validate::make($array,[
            "name"=>"required|string",
            "surname1"=>"required|string",
            "surname2"=>"required|string",
            "registration"=>"required|string",
            "password"=>"required|string"
        ]);
    }

}

?>