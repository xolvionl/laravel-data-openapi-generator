<?php

use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Route;

beforeAll(function () {
    include __DIR__ . '/../../src/routes/routes.php';
});

it('creates routes', function () {
    $routes = array_map(
        fn (RoutingRoute $route) => $route->uri,
        Route::getRoutes()->getRoutes()
    );

    expect($routes)
        ->toBe([
            'api/openapi',
            'api/openapi.json',
        ]);
});
