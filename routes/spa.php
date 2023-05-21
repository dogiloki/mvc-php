<?php

$router->post('/component/{name}',function($request){
    return component($request->name,$request->all());
});

?>