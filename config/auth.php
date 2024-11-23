<?php

return [

    'web'=>[
        'driver'=>'session',
        'model'=>app\Models\User::class
    ],

    'api'=>[
        'driver'=>'token',
        'model'=>app\Models\User::class
    ],

    'session'=>[
        'id_user'=>'auth',
        'remember_token'=>'token'
    ],

    'pam_auth'=>true

];

?>