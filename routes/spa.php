<?php

$router->post('/component/{name}',function($request){
    $component=component($request->input('name'));
    ob_start();
    $component->init($request);
    $html=ob_get_clean();
    $vars=$component->getVars();
    return json([
        "html"=>$html,
        "vars"=>$vars
    ]);
});

?>