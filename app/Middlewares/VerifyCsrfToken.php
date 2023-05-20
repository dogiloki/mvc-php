<?php

namespace app\Middlewares;

use libs\Middle\Middleware;

class VerifyCsrfToken extends Middleware{

    /**
     * Verificar si llamar a redirectTo()
     * @return bool
     */
	public function handle($request){
		$csrf_token=$request->input('_token')??$request->header('X-CSRF-TOKEN');
        if($csrf_token==null){
            return false;
        }
        return $csrf_token==csrfToken();
	}
	
	/**
     * Redireccionar a una ruta
     */
	public function redirectTo($request){
        return $_SERVER['REQUEST_METHOD']=='GET'?null:abort(419);
	}
	
	/**
     * Ejecución al finalizar
     */
	public function terminate($request){
		
	}

}

?>