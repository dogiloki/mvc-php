<?php

use models\User;
use libs\Middle\Secure;
use libs\Auth\Auth;
use libs\Middle\Session;
use libs\Middle\Log;
use libs\Middle\Storage;

$router->post('/test',function($request){
    echo $request->input('name');
})->name('test');

$router->get('/test',function($request){
    echo csrfToken();
});

?>