<?php

return [

    'web'=>[
        'driver'=>'session',
        'model'=>models\User::class
    ],

    'api'=>[
        'driver'=>'token',
        'model'=>models\User::class
    ]

];

?>