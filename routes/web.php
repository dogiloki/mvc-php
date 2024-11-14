<?php

use libs\Router\Route;

Route::get('/',function(){
    return view('home');
})->name('home');

Route::post('/component/{name?}',function($request){
    $class_name="app\\Components\\".$request->name;
    $component=new $class_name();
    if($request->set_properties){
        $component->setProperties(json_decode($request->properties));
    }
    $component->view();
    $content=ob_get_clean();
    return json([
        'content'=>$content,
        'properties'=>$component->getProperties()
    ]);
})->name('component');

Route::post('/component-data-table',function($request){
    $class_name="app\\Components\\".$request->name;
    $component=new $class_name();
    $component->selectColumns(json_decode($request->select_columns));
    $component->withMethods(json_decode($request->with_methods));
    return json($component->dataTable());
})->name('component-data-table');

?>