<?php

namespace app\Middlewares;

use libs\Middle\Middleware;
use libs\Auth\Auth;

class Authenticate extends Middleware{

    /**
     * Verificar si llamar a redirectTo()
     * @return bool
     */
    public function handle($request){
        return Auth::check();
    }
    
    /**
     * Redireccionar a una ruta
     */
    public function redirectTo($request){
        return redirect(route('login'));
    }

    /**
     * Ejecución al finalizar
     */
    public function terminate($request){
        
    }

}

?>