<?php

namespace Xolvio\OpenApiGenerator\Data;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\TransformationType;

class Content extends Data
{
    public function __construct(
        protected string $type,
        public Schema $schema,
    ) {
    }

    public static function fromReflection(ReflectionNamedType $type, ReflectionMethod|ReflectionFunction $method): self
    {
        return new self(
            type: 'application/json',
            schema: Schema::fromDataReflection($type, $method),
        );
    }

    /**
     * @return array<int|string,mixed>
     */
    public function transform(TransformationType $type): array
    {
        return [
            $this->type => parent::transform($type),
        ];
    }
}
