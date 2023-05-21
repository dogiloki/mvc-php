<?php

return [

    'routers'=>[

        'web'=>[
            libs\Session\Middleware\StartSession::class,
            app\Middlewares\VerifyCsrfToken::class
        ],

        'api'=>[

        ]

    ],
    
    'alias'=>[
        'auth'=>app\Middlewares\Authenticate::class,
        'guest'=>app\Middlewares\Guest::class,
        'verify_email'=>app\Middlewares\VerifyEmail::class
    ]
    
];

?>