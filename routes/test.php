<?php

use libs\Router\Route;
use libs\HTTP\Request;
use libs\Validator\Validate;
use libs\Middle\Secure;
use libs\Config;
use libs\Middle\QR\QRCode;
use libs\Middle\Email;
use libs\Middle\Storage;

Route::get('/test/{name?}',function(Request $request){

})->name('test-get');

Route::post('/test',function(Request $request){

})->name('test-post');

?>