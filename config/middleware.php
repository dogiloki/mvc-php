<?php

return [

    'routers'=>[

        'web'=>[
            
        ],

        'api'=>[

        ]

    ],

    /**
     * No puntos en las claves
     */
    'alias'=>[
        'auth'=>app\Middlewares\Authenticate::class,
        'guest'=>app\Middlewares\Guest::class,
        'verify_email'=>app\Middlewares\VerifyEmail::class
    ]
    
];

?>