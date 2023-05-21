<?php

$router->get('/',function(){
    return view('index');
})->name('home');

?>