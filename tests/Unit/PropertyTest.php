<?php

use Xolvio\OpenApiGenerator\Data\Property;
use Xolvio\OpenApiGenerator\Test\ContentTypeData;
use Xolvio\OpenApiGenerator\Test\NotData;
use Xolvio\OpenApiGenerator\Test\RequestData;
use Xolvio\OpenApiGenerator\Test\ReturnData;

it('cannot create property from non data class', function () {
    foreach ([NotData::class, 'string', 'integer', 'asdf'] as $class) {
        expect(fn () => Property::fromDataClass($class))
            ->toThrow(RuntimeException::class);
    }
});

it('can create properties from data class', function () {
    foreach ([ReturnData::class, ContentTypeData::class] as $class) {
        $properties = Property::fromDataClass($class);
        $types      = array_map(fn (Property $item) => $item->type->toArray(), $properties->all());

        expect($types)
            ->toBe([
                [
                    'type' => 'string',
                ],
            ]);
    }

    $properties = Property::fromDataClass(RequestData::class);
    $types      = array_map(fn (Property $item) => $item->type->toArray(), $properties->all());

    expect($types)
        ->toBe([
            [
                'type' => 'integer',
            ],
            [
                'type'     => 'integer',
                'nullable' => true,
            ],
            [
                'type' => 'string',
            ],
            [
                'type'     => 'string',
                'nullable' => true,
            ],
            [
                'type' => 'boolean',
            ],
            [
                'type'     => 'boolean',
                'nullable' => true,
            ],
            [
                'type' => 'number',
            ],
            [
                'type'     => 'number',
                'nullable' => true,
            ],
            [
                'nullable' => true,
                'allOf'    => [
                    ['$ref' => '#/components/schemas/RequestData'],
                ],
            ],
            [
                '$ref' => '#/components/schemas/ReturnData',
            ],
            [
                'nullable' => true,
                'allOf'    => [
                    ['$ref' => '#/components/schemas/ReturnData'],
                ],
            ],
        ]);
});

it('can create property from reflection', function () {
    foreach ([RequestData::class, ReturnData::class, ContentTypeData::class] as $class) {
        $reflection = new ReflectionClass($class);

        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $reflection_property) {
            $property = Property::fromProperty($reflection_property);

            expect($property->getName())
                ->toBe($reflection_property->getName());
        }
    }
});
