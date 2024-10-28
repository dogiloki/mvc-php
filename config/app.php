<?php

return [

    'debug'=>(bool)env('APP_DEBUG',true),
    'host'=>env('APP_HOST'),
    'key'=>env('APP_KEY'),
    'locale'=>env('APP_LOCALE','es'),
    'faker_locale'=>env('FAKER_LOCALE','es_ES'),
    'timezone'=>env('APP_TIMEZONE','America/Mexico_City')

];

?>