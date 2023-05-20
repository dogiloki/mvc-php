<?php

return [

    'web'=>[
        'driver'=>'session',
        'model'=>app\Models\User::class
    ],

    'api'=>[
        'driver'=>'token',
        'model'=>app\Models\User::class
    ]

];

?>