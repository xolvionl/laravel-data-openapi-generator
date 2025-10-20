<?php

namespace Xolvio\OpenApiGenerator\Data;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;
use RuntimeException;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Data as LaravelData;

class Property extends Data
{
    public function __construct(
        protected string $name,
        public Schema $type,
        public bool $required = true,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int,self>
     */
    public static function fromDataClass(string $class): Collection
    {
        if (! is_a($class, LaravelData::class, true)) {
            throw new RuntimeException('Class does not extend LaravelData');
        }

        $reflection = new ReflectionClass($class);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        return self::collect(
            array_map(
                fn (ReflectionProperty $property) => self::fromProperty($property),
                $properties
            ),
            Collection::class
        );
    }

    public static function fromProperty(ReflectionProperty $reflection): self
    {
        return new self(
            name: $reflection->getName(),
            type: Schema::fromReflectionProperty($reflection),
            required: ! $reflection->getType()?->allowsNull() ?? false,
        );
    }
}
