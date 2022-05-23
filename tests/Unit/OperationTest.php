<?php

use Illuminate\Routing\Route;
use Xolvio\OpenApiGenerator\Data\DefaultResponse;
use Xolvio\OpenApiGenerator\Data\Operation;
use Xolvio\OpenApiGenerator\Data\RequestBody;
use Xolvio\OpenApiGenerator\Test\Controller;

it('can create operation without parameters', function () {
    foreach (['basic', 'array', 'collection', 'requestBasic', 'requestNoData', 'contentType'] as $function) {
        $route = new Route('get', '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->parameters)
            ->toBeNull();
    }
});

it('can create operation with single parameter', function () {
    foreach (['intParameter', 'stringParameter', 'modelParameter'] as $function) {
        $route = new Route('get', '/{parameter}', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->parameters)
            ->toHaveLength(1);
    }
});
it('can create operation with multiple parameters', function () {
    $route = new Route('get', '/{parameter_1}/{parameter_2}/{parameter_3}', [Controller::class, 'allCombined']);
    $route->setContainer(app());

    $operation = Operation::fromRoute($route);

    expect($operation->parameters)
        ->toHaveLength(3);
});
it('can create operation without request body', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData'] as $function) {
        $route = new Route('get', '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->requestBody)
            ->toBeNull();
    }
});
it('can create operation with request body', function () {
    foreach (['requestBasic', 'allCombined', 'contentType'] as $function) {
        $route = new Route('get', '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->requestBody)
            ->toBeInstanceOf(RequestBody::class);
    }
});
it('can create operation with response', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData', 'requestBasic', 'allCombined', 'contentType'] as $function) {
        $route = new Route('get', '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->responses)
            ->toBeInstanceOf(DefaultResponse::class);
    }
});
it('can create operation without security', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData', 'requestBasic', 'allCombined', 'contentType'] as $function) {
        $route = new Route('get', '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->security)
            ->toBeNull();
    }
});
it('can create operation with security', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData', 'requestBasic', 'allCombined', 'contentType'] as $function) {
        $route = new Route('get', '/', [Controller::class, $function]);
        $route->middleware('auth:sanctum');
        $route->setContainer(app());

        $operation = Operation::fromRoute($route);

        expect($operation->security)
            ->toHaveLength(1);
    }
});
