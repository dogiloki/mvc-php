<?php

use libs\Router\Route;
use libs\HTTP\Request;
use libs\Validator\Validate;

Route::get('/test/{id?}',function(Request $request){
    /*
    $validation=Validate::make(
        ["name"=>"Julio","surname"=>"Vilalnueva","edad"=>25,"email"=>"julio@gmail.com"],
        ["name"=>"required|string","surname"=>"required|string","edad"=>"between:11,25","email"=>"email"]
    );
    dd($validation->errors());
    */
    libs\Middle\Storage::deleteHash($request->id);
})->name('test-get');

Route::post('/test',function(Request $request){
    libs\Middle\Storage::upload($request->files('name'),"files");
})->name('test-post');

?>