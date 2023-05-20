<?php

return [

    'routers'=>[

        'web'=>[
            
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