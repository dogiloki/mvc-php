<?php

namespace app\Services;

use libs\Service\Contract\ServiceImpl;
use libs\Middleware\Middleware;

class MiddlewareService implements ServiceImpl{

	public function handle(){
		// Middleware globales
		Middleware::middleware([
			\libs\Session\Middleware\StartSession::class,
			\app\Middlewares\VerifyCsrfToken::class
		]);
		// Middleware archivos en específico

		// Middleware alias
		Middleware::middlewareAlias([
			'auth'=>\app\Middlewares\Authenticate::class,
			'auth_api'=>\app\Middlewares\AuthenticateApi::class,
			'guest'=>\app\Middlewares\Guest::class,
			'verify_email'=>\app\Middlewares\VerifyEmail::class,
			'csrf'=>\app\Middlewares\VerifyCsrfToken::class,
			'session'=>\libs\Session\Middleware\StartSession::class
		]);
	}

	public function terminate(){
		
	}

	public function report($ex){
		exception($ex);
	}

}

?>