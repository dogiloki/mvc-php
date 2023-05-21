<?php

return [

    'driver'=>env('SESSION_DRIVER','file'),
    'encrypt'=>env('SESSION_ENCRYPT',false),

    'file'=>[
        'path'=>env('SESSION_PATH',storagePath('framework/sessions')),
    ],
    
    'cookie'=>[
        'name'=>env('SESSION_COOKIE_NAME','SESSION_PHP'),
        'lifetime'=>env('SESSION_COOKIE_LIFETIME',120),
        'path'=>env('SESSION_COOKIE_PATH','/'),
        'domain'=>env('SESSION_COOKIE_DOMAIN',null),
        'secure'=>env('SESSION_COOKIE_SECURE',false),
        'httponly'=>env('SESSION_COOKIE_HTTPONLY',true),
        'samesite'=>env('SESSION_COOKIE_SAMESITE','lax'),
    ]

];

?>