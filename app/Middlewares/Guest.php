<?php

namespace app\Middlewares;
		
use libs\Middle\Middleware;
use libs\Auth\Auth;
		
class Guest extends Middleware{

	/**
     * Verificar si llamar a redirectTo()
     * @return bool
     */
	public function handle($request){
		return !Auth::check();
	}
	
	/**
     * Redireccionar a una ruta
     */
	public function redirectTo($request){
		return redirect(route('home'));
	}
	
	/**
     * Ejecución al finalizar
     */
	public function terminate($request){
		
	}
		
}
		
?>