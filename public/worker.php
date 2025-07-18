<?php

// FrankenPHP Worker Script untuk Laravel
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__ . '/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel once for the worker...
/** @var Application $app */
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Handle requests in worker mode
while (frankenphp_handle_request(function (Request $request) use ($app) {
    // Process the request
    $response = $app->handle($request);

    // Send the response
    $response->send();

    // Clean up after request
    $app->terminate($request, $response);

    // Clear resolved instances for next request
    $app->forgetScopedInstances();

    return $response;
})) {
    // Reset any global state if needed
    gc_collect_cycles();
}
