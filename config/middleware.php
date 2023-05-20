<?php

return [

    'routers'=>[

        'web'=>[
            app\Middlewares\VerifyCsrfToken::class
        ],

        'api'=>[

        ]

    ],

    'alias'=>[
        'auth'=>app\Middlewares\Authenticate::class,
        'guest'=>app\Middlewares\Guest::class
    ]
    
];

?>