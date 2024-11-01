<?php

use libs\Router\Route;
use libs\HTTP\Request;
use libs\Validator\Validate;
use libs\Middle\Secure;
use libs\Config;
use libs\Middle\QR\QRCode;
use libs\Middle\Email;

Route::get('/test/{id?}',function(Request $request){
    
})->name('test-get');

Route::post('/test',function(Request $request){
    dd($request->name);
})->name('test-post');

?>