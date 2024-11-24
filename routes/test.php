<?php

use libs\Router\Route;
use libs\HTTP\Request;
use libs\Validator\Validate;
use libs\Middle\Secure;
use libs\Config;
use libs\Middle\QR\QRCode;
use libs\Middle\Email;
use libs\Middle\Storage;
use libs\System\Process;
use app\Models\User;
use libs\Auth\Auth;

Route::get('/test/{name?}',function(Request $request){
    Auth::attempt([
        'user'=>'usuario',
        'password'=>'123456'
    ]);
    dd(Auth::user());
    Auth::logout();
})->name('test-get');

Route::post('/test',function(Request $request){

})->name('test-post');

?>
