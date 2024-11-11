<?php

use libs\Router\Route;

Route::get('/',function(){
    return view('home');
})->name('home');

Route::post('/component/{name?}',function($request){
    $class_name="app\\Components\\".$request->name;
    $component=new $class_name();
    $component->setProperties(json_decode($request->properties));
    return json([
        'properties'=>$component->getProperties()
    ]);
})->name('component');

?>