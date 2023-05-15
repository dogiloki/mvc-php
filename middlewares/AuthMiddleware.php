<?php

namespace middlewares;

use libs\Middle\Middle;
use libs\Middle\Auth;

class AuthMiddleware extends Middle{

    /**
     * Verificar si llamar a redirectTo()
     * @return bool
     */
    public function handle(){
        return Auth::check();
    }
    
    /**
     * Redireccionar a una ruta
     */
    public function redirectTo(){
        return redirect(route('login'));
    }

    /**
     * Ejecución al finalizar
     */
    public function terminate(){
        
    }

}

?>