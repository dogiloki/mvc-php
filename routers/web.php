<?php

use models\User;
use libs\Middle\Secure;
use libs\Auth\Auth;
use libs\Middle\Session;

$router->get('/',function(){
    return view('index');
});

?>