<?php

use libs\Router\Route;

Route::post('api/document','DocumentController@register')->name('api-create-document');

?>