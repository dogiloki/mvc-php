<?php

return [

    /**
     * Autentication
     */

    'auth'=>[
        'model'=>\models\User::class,
        'session'=>'session_user'
    ],

    /**
     * Middlewares
     */

    'middle'=>[
        'auth'=>\middlewares\AuthMiddleware::class
    ],

    /**
     * Database
     */

    'database'=>[
        'engine'=>'InnoDB',
        'charset'=>'utf8mb4'
    ]

]

?>