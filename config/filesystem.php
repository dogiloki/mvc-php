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

    'components'=>[
        'path'=>'app/Components'
    ],

    'scss'=>[
        'path_input'=>'resources/scss',
        'path_output'=>'public/css',
        'path_public'=>'css'
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

    'cache'=>[
        'path'=>'storage/framework/cache',
        'file'=>'files.json'
    ],

    'create'=>[
        'folders'=>[
            'storage',
            'storage/app',
            'storage/app/public',
            'storage/logs',
            'storage/framework',
            'storage/framework/cache',
            'storage/framework/sessions',
            'storage/framework/views'
        ],
        'files'=>[
            [
                'path'=>'config.env',
                'content'=>file_get_contents('config.env.example')
            ],
            [
                'path'=>'storage/framework/cache/files.json',
                'content'=>''
            ]
        ]
    ]

];

?>