<?php

return [

    'storage'=>[
        'path'=>'storage',
        'app'=>'storage/app',
        'public'=>'storage/app/public',
        'files'=>'storage/app/files'
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

    'middlewares'=>[
        'path'=>'app/Middlewares'
    ],

    'services'=>[
        'path'=>'app/Services'  
    ],

    'components'=>[
        'path'=>'app/Components'
    ],

    'lang'=>[
        'path'=>'resources/lang'
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
        /*
        'folders'=>[
            'example-folder'
        ],
        */
        'files'=>[
            [
                'path'=>'config.env',
                'content'=>file_get_contents('config.env.example')
            ]
        ]
    ]

];

?>