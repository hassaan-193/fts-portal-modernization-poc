<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = [];
foreach (app('router')->getRoutes() as $route) {
    $routes[] = [
        'methods' => $route->methods(),
        'uri' => $route->uri(),
        'name' => $route->getName(),
        'action' => $route->getActionName(),
        'middleware' => $route->middleware(),
    ];
}

file_put_contents(__DIR__ . '/routes_dump.json', json_encode($routes, JSON_PRETTY_PRINT));
echo "Successfully dumped " . count($routes) . " routes to scratch/routes_dump.json\n";
