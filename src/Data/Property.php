<?php

namespace Xolvio\OpenApiGenerator\Data;

use ReflectionClass;
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

    /**
     * @return DataCollection<int,self>
     */
    public static function fromDataClass(string $class): DataCollection
    {
        if (! is_a($class, LaravelData::class, true)) {
            throw new \RuntimeException('Class does not extend LaravelData');
        }

        /** @var class-string<LaravelData> $class */
        $data_class = DataClass::create(new ReflectionClass($class));

        $properties = $data_class->properties
            ->map(fn (DataProperty $property) => self::fromProperty($property));

        /** @var DataCollection<int,self> */
        $collection = self::collection($properties->all());

        return $collection;
    }

    public static function fromProperty(DataProperty $property): self
    {
        return new self(
            name: $property->name,
            type: Schema::fromDataPropery($property),
        );
    }
}
