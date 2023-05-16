<?php

return [

    'name'=>'SESSION_PHP',
    'lifetime'=>120,
    'path'=>'/',
    'domain'=>env('SESSION_DOMAIN'),
    'secure'=>false,
    'httponly'=>true,
    'samesite'=>'lax'

];

?>