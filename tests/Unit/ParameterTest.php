<?php

use Illuminate\Routing\Route;
use Xolvio\OpenApiGenerator\Data\Parameter;

it('can create no parameter', function () {
    foreach (['basic', 'requestBasic'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(Parameter::fromRoute($route, $method))
            ->toHaveLength(0);
    }
});

it('can create int parameter', function () {
    $route  = new Route('get', '/{parameter}', [Controller::class, 'intParameter']);
    $method = methodFromRoute($route);

    expect(Parameter::fromRoute($route, $method)?->toArray())
        ->toBe([[
            'name'        => 'parameter',
            'in'          => 'path',
            'description' => 'parameter',
            'required'    => true,
            'schema'      => [
                'type' => 'integer',
            ],
        ]]);
});

it('can create string parameter', function () {
    $route  = new Route('get', '/{parameter}', [Controller::class, 'stringParameter']);
    $method = methodFromRoute($route);

    expect(Parameter::fromRoute($route, $method)?->toArray())
        ->toBe([[
            'name'        => 'parameter',
            'in'          => 'path',
            'description' => 'parameter',
            'required'    => true,
            'schema'      => [
                'type' => 'string',
            ],
        ]]);
});

it('can create model parameter', function () {
    $route  = new Route('get', '/{parameter}', [Controller::class, 'modelParameter']);
    $method = methodFromRoute($route);

    expect(Parameter::fromRoute($route, $method)?->toArray())
        ->toMatchArray([[
            'name'        => 'parameter',
            'in'          => 'path',
            'description' => 'parameter',
            'required'    => true,
            'schema'      => [
                'type' => 'integer',
            ],
        ]]);
});
it('can create multiple parameters', function () {
    $route  = new Route('get', '/{parameter_1}/{parameter_2}/{parameter_3}', [Controller::class, 'allCombined']);
    $method = methodFromRoute($route);

    expect(Parameter::fromRoute($route, $method)?->toArray())
        ->toMatchArray([[
            'name'        => 'parameter_1',
            'in'          => 'path',
            'description' => 'parameter_1',
            'required'    => true,
            'schema'      => [
                'type' => 'integer',
            ],
        ], [
            'name'        => 'parameter_2',
            'in'          => 'path',
            'description' => 'parameter_2',
            'required'    => true,
            'schema'      => [
                'type' => 'string',
            ],
        ], [
            'name'        => 'parameter_3',
            'in'          => 'path',
            'description' => 'parameter_3',
            'required'    => true,
            'schema'      => [
                'type' => 'integer',
            ],
        ]]);
});
