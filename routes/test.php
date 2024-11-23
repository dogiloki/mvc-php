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

Route::get('/test/{name?}',function(Request $request){
    $process=new Process('php -r "sleep(5); echo \'Proceso de 5 segundos terminado\';"');
    $process->start();
    dd($process->output());
})->name('test-get');

Route::post('/test',function(Request $request){

})->name('test-post');

?>