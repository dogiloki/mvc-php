<?php

use libs\HTTP\Request;

$router->get('/saludar/{nombre}/{edad?}',function(Request $request){
    return view('');
});


?>