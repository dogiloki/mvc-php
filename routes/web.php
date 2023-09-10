<?php

use libs\HTTP\Request;

$router->get('/',function(Request $request){
    return route('home');
})->name('home');


?>