<?php

return [

    'storage'=>[
        'path'=>'storage',
        'app'=>'storage/app',
        'public'=>'storage/app/public',
    ],

    'public'=>[
        'path'=>'public',
        'url'=>url()."public",
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
        'path'=>'app/Controllers'
    ],

    'models'=>[
        'path'=>'app/Models'
    ],

    'views'=>[
        'path'=>'resources/views',
        'cache'=>'storage/framework/views',
    ],

    'logs'=>[
        'path'=>'storage/logs',
        'file'=>'storage/logs/app.log',
        'channels'=>[
            'info'=>[
                'path'=>'storage/logs/info',
            ],
            'error'=>[
                'path'=>'storage/logs/error',
            ],
            'warning'=>[
                'path'=>'storage/logs/warning',
            ],
            'debug'=>[
                'path'=>'storage/logs/debug',
            ],
            'notice'=>[
                'path'=>'storage/logs/notice',
            ],
            'critical'=>[
                'path'=>'storage/logs/critical',
            ],
            'alert'=>[
                'path'=>'storage/logs/alert',
            ],
            'emergency'=>[
                'path'=>'storage/logs/emergency',
            ]
        ],
    ],

];

?>