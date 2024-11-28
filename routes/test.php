<?php

use libs\Router\Route;
use libs\HTTP\Request;
use libs\Validator\Validate;
use libs\Middle\Secure;
use libs\Config;
use libs\Middle\QR\QRCode;
use libs\Middle\Email;
use libs\Session\Session;
use libs\Middle\Storage;
use libs\System\Process;
use app\Models\User;
use libs\Permission\Models\Role;
use libs\Auth\Auth;

Route::get('/test/{name?}',function(Request $request){
    $user=Auth::user();
})->name('test-get');

Route::post('/test',function(Request $request){

})->name('test-post');

?>
