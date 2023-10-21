<?php

use libs\HTTP\Request;

$router->get('/test',function(Request $request){
    return "soy un test";
})->name('test-get');

$router->post('/test',function(Request $request){
    return $request->name;
})->name('test-post');

?>