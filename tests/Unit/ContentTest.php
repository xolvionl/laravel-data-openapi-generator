<?php

use Illuminate\Routing\Route;
use Spatie\LaravelData\Data;
use Xolvio\OpenApiGenerator\Data\Content;
use Xolvio\OpenApiGenerator\Data\RequestBody;

it('can create default content', function () {
    foreach (['requestBasic', 'allCombined'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(Content::fromReflection(RequestBody::getFirstOfClassType($method, Data::class), $method)->toArray())
            ->toBe([
                'application/json' => [
                    'schema' => [
                        '$ref' => '#/components/schemas/RequestData',
                    ],
                ],
            ]);
    }
});

it('can create custom content', function () {
    $route  = new Route('get', '/', [Controller::class, 'contentType']);
    $method = methodFromRoute($route);

    expect(Content::fromReflection(RequestBody::getFirstOfClassType($method, Data::class), $method)->toArray())
        ->toBe([
            'application/json' => [
                'schema' => [
                    '$ref' => '#/components/schemas/ContentTypeData',
                ],
            ],
            'application/xml' => [
                'schema' => [
                    '$ref' => '#/components/schemas/ContentTypeData',
                ],
            ],
        ]);
});
