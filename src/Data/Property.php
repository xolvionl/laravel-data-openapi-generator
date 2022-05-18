<?php

namespace Xolvio\OpenApiGenerator\Data;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use Spatie\Invade\Invader;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Data as LaravelData;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\DataClass;
use Spatie\LaravelData\Support\DataProperty;

class Property extends Data
{
    public function __construct(
        protected string $name,
        public Schema $type,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public static function fromDataClass(string $class): DataCollection
    {
        if (! is_a($class, LaravelData::class, true)) {
            throw new \RuntimeException('Class does not extend LaravelData');
        }

        /** @var class-string<LaravelData> $class */
        $data_class = new DataClass(new ReflectionClass($class));

        $properties = $data_class->properties()
            ->map(static function (DataProperty $property) {
                /** @var ReflectionProperty */
                $reflection = (new Invader($property))->property; // @phpstan-ignore-line Invader not supported by phpstan

                return self::fromProperty($reflection);
            });

        return self::collection($properties->all());
    }

    public static function fromProperty(ReflectionProperty $property): self
    {
        $type = $property->getType();

        if (! $type instanceof ReflectionNamedType) {
            throw new \RuntimeException('Type is not named');
        }

        return new self(
            name: $property->getName(),
            type: Schema::fromDataReflection($type, $property),
        );
    }
}
