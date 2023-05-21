<?php

namespace libs\Middle;

USE libs\HTTP\Request;

abstract class Middleware{

    /**
     * Se llama automáticamente cuando se ejecuta el middleware
     */
    public function handle(Request $request, \Closure $next){
        return $next($request);
    }

    /**
     * Se llama después de que se ejecuta el middleware
     */
    public function terminate(Request $request, \Closure $response){

    }

    /**
     * Reportar excepciones o errores
     */
    public function report(\Exception $ex){

    }

}

?>