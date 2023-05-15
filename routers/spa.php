<?php

$router->get('/var/variable',function(){
    $variable='Soy una variable';
    return json(compact('variable'));
});

$router->get('/component/vista',function(){
    return view('components.vista');
});

?>