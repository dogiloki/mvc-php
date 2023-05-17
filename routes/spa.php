<?php

$router->post('/var',function(){
    $variable='Soy una variable';
    return json(compact('variable'));
});

$router->post('/component/vista',function($request){
    $variable=$request->variable??null;
    return view('components.vista',compact('variable'));
});

?>