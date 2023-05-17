<?php

use models\User;
use libs\Middle\Secure;
use libs\Auth\Auth;
use libs\Middle\Session;
use libs\Middle\Log;

$router->get('/',function(){
    Log::info("Hello World!");
    Log::error("Hello World!");
    Log::warning("Hello World!");
    Log::debug("Hello World!");
    Log::notice("Hello World!");
    Log::critical("Hello World!");
    Log::alert("Hello World!");
    Log::emergency("Hello World!");
    Log::channel("warning","Hello World!");
    return view('index');
});

?>