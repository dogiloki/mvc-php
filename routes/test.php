<?php

use libs\Router\Route;
use libs\HTTP\Request;
use libs\Validator\Validate;
use libs\Middle\Secure;
use app\Validators\UserValidator;

Route::get('/test/{id?}',function(Request $request){
    /*
    UserValidator::store([
        "name"=>"Julio"
    ]);
    */
    //echo Secure::encodePassword("123");
    $validation=Validate::make(
        ["name"=>"Julio","surname"=>"Vilalnueva","edad"=>25,"email"=>"julio@gmail.com"],
        ["name"=>"required|string","surname"=>"required|string","edad"=>"between:11,25","email"=>"email"]
    );
    dd($validation->errors());
    
})->name('test-get');

Route::post('/test',function(Request $request){
    dd($request->name);
})->name('test-post');

?>