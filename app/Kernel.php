<?php

namespace app;

use libs\KernelApp;

class Kernel extends KernelApp{

    protected $services=[
        \app\Services\ReportService::class,
        \app\Services\RulesService::class,
        \app\Services\MiddlewareService::class,
        \libs\Service\RoutingService::class,
        \app\Services\RouteService::class,
    ];

}

?>