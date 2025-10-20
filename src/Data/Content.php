<?php

namespace Xolvio\OpenApiGenerator\Data;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Support\Transformation\TransformationContextFactory;
use Xolvio\OpenApiGenerator\Attributes\CustomContentType;

class Content extends Data
{
    public function __construct(
        /** @var string[] */
        protected array $types,
        public Schema $schema,
    ) {}

    public static function fromReflection(ReflectionNamedType $type, ReflectionFunction|ReflectionMethod $method): self
    {
        return new self(
            types: self::typesFromReflection($type),
            schema: Schema::fromDataReflection($type, $method),
        );
    }

    public static function fromClass(string $class, ReflectionFunction|ReflectionMethod $method): self
    {
        $type = $method->getReturnType();

        return new self(
            types: self::typesFromReflection($type),
            schema: Schema::fromDataReflection($class),
        );
    }

    /**
     * @return array<int|string,mixed>
     */
    public function transform(
        null|TransformationContext|TransformationContextFactory $transformationContext = null,
    ): array {
        return collect($this->types)->mapWithKeys(
            fn (string $content_type) => [$content_type => parent::transform($transformationContext)]
        )->toArray();
    }

    /**
     * @return string[]
     */
    protected static function typesFromReflection(null|ReflectionNamedType|ReflectionType $type): array
    {
        if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
            /** @var class-string $name */
            $name       = $type->getName();
            $reflection = new ReflectionClass($name);

            $custom_content_attribute = $reflection->getAttributes(CustomContentType::class);

            if (count($custom_content_attribute) > 0) {
                return $custom_content_attribute[0]->getArguments()['type'];
            }
        }

        return ['application/json'];
    }
}
