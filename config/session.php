<?php

return [

    'driver'=>env('SESSION_DRIVER','database'),
    'encrypt'=>env('SESSION_ENCRYPT',false),

    'file'=>[
        'path'=>env('SESSION_PATH',storagePath('framework/sessions')),
    ],

    'database'=>[
        'table'=>env('SESSION_TABLE','session')
    ],
    
    'cookie'=>[
        'name'=>env('SESSION_COOKIE_NAME','SESSION_PHP'),
        'lifetime'=>env('SESSION_COOKIE_LIFETIME',120),
        'path'=>env('SESSION_COOKIE_PATH','/'),
        'domain'=>env('SESSION_COOKIE_DOMAIN',null),
        'secure'=>env('SESSION_COOKIE_SECURE',false),
        'httponly'=>env('SESSION_COOKIE_HTTPONLY',true),
        'samesite'=>env('SESSION_COOKIE_SAMESITE','Lax'),
        'payload'=>[
            'csrf_token'=>'_csrf_token'
        ]
    ]

];

?>