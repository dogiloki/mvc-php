<?php

$router->post('/component/{name}',function($request){
    return component($request->input('name'),$request->all());
});

?>