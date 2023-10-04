<?php

return [

    'routers'=>[

        'web'=>[
            libs\Session\Middleware\StartSession::class,
            app\Middlewares\VerifyCsrfToken::class
        ],

        'test'=>[
            libs\Session\Middleware\StartSession::class,
            app\Middlewares\VerifyCsrfToken::class
        ],

        'api'=>[
            
        ]

    ],
    
    'alias'=>[
        'auth'=>app\Middlewares\Authenticate::class,
        'auth_api'=>app\Middlewares\AuthenticateApi::class,
        'guest'=>app\Middlewares\Guest::class,
        'verify_email'=>app\Middlewares\VerifyEmail::class,
        'csrf'=>app\Middlewares\VerifyCsrfToken::class,
        'session'=>libs\Session\Middleware\StartSession::class
    ]
    
];

?>