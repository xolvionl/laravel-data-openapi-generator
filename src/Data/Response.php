<?php

namespace Xolvio\OpenApiGenerator\Data;

use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;
use Spatie\LaravelData\Data;

class Response extends Data
{
    public function __construct(
        public string $description,
        public Content $content,
    ) {
    }

    public static function fromRoute(ReflectionMethod|ReflectionFunction $method): self
    {
        $type = $method->getReturnType();

        if (! $type instanceof ReflectionNamedType) {
            throw new RuntimeException('Method does not have a return type');
        }

        return new self(
            description: $method->getName(),
            content: Content::fromReflection($type, $method),
        );
    }
}
