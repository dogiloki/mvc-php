<?php

return [

    'storage'=>[
        'app'=>'storage/app',
        'public'=>'storage/app/public',
    ],

    'public'=>[
        'path'=>'public',
        'url'=>url()."/public",
    ],

    'database'=>[
        'path'=>'database',
        'migrations'=>'database/migrations',
        'seeds'=>'database/seeds',
    ],

    'routes'=>[
        'path'=>'routes'
    ],

    'controllers'=>[
        'path'=>'controllers'
    ],

    'models'=>[
        'path'=>'models'
    ],

    'views'=>[
        'path'=>'views',
        'cache'=>'storage/framework/views',
    ],

    'logs'=>[
        'path'=>'storage/logs',
        'file'=>'storage/logs/app.log'
    ],

];

?>