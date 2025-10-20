<?php

use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Xolvio\OpenApiGenerator\Data\Operation;
use Xolvio\OpenApiGenerator\Data\RequestBody;
use Xolvio\OpenApiGenerator\Data\Response;
use Xolvio\OpenApiGenerator\Test\Controller;

it('can create operation without parameters', function () {
    foreach (['basic', 'array', 'collection', 'requestBasic', 'requestNoData', 'contentType'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->parameters)
            ->toBeNull('');
    }
});

it('can create operation with single parameter', function () {
    foreach (['intParameter', 'stringParameter', 'modelParameter'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/{parameter}', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->parameters)
            ->toHaveLength(1);
    }
});
it('can create operation with multiple parameters', function () {
    $method = 'post';
    $route  = new Route($method, '/{parameter_1}/{parameter_2}/{parameter_3}', [Controller::class, 'allCombined']);
    $route->setContainer(app());

    $operation = Operation::fromRoute($route, $method);

    expect($operation->parameters)
        ->toHaveLength(3);
});
it('can create operation without request body', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->requestBody)
            ->toBeNull();
    }
});
it('can create operation with request body', function () {
    foreach (['requestBasic', 'allCombined', 'contentType'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->requestBody)
            ->toBeInstanceOf(RequestBody::class);
    }
});
it('can create operation with response', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData', 'requestBasic', 'allCombined', 'contentType'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->responses)
            ->toBeInstanceOf(Collection::class);

        foreach ($operation->responses->all() as $status_code => $response) {
            expect($status_code)
                ->toBe(HttpResponse::HTTP_OK);
            expect($response)
                ->toBeInstanceOf(Response::class);
        }
    }
});
it('can create operation without security', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData', 'requestBasic', 'allCombined', 'contentType'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/', [Controller::class, $function]);
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->security)
            ->toBeNull();
    }
});
it('can create operation with security', function () {
    foreach (['basic', 'array', 'collection', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData', 'requestBasic', 'allCombined', 'contentType'] as $function) {
        $method = 'post';
        $route  = new Route($method, '/', [Controller::class, $function]);
        $route->middleware('auth:sanctum');
        $route->setContainer(app());

        $operation = Operation::fromRoute($route, $method);

        expect($operation->security)
            ->toHaveLength(1);
    }
});
it('can create operation without description', function () {
    $method = 'post';
    $route  = new Route($method, '/', [Controller::class, 'basic']);
    $route->setContainer(app());

    $operation = Operation::fromRoute($route, $method);

    expect($operation->description)
        ->toBeNull();
});
it('can create operation with permissions description', function () {
    $method = 'post';
    $route  = new Route($method, '/', [Controller::class, 'basic']);
    $route->middleware('can:permission1');
    $route->middleware('auth:sanctum');
    $route->setContainer(app());

    expect(Operation::fromRoute($route, $method)->description)
        ->toBe('Permissions needed: permission1');

    $route->middleware('can:permission2');

    $operation = Operation::fromRoute($route, $method);
    expect($operation->description)
        ->toBe('Permissions needed: permission1, permission2');

    $status_codes = array_keys($operation->responses->all());

    expect($status_codes)
        ->toBe([
            HttpResponse::HTTP_OK,
            HttpResponse::HTTP_UNAUTHORIZED,
            HttpResponse::HTTP_FORBIDDEN,
        ]);

    foreach ($operation->responses->all() as $status_code => $response) {
        expect($response)
            ->toBeInstanceOf(Response::class);
    }
});
