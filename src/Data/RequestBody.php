<?php

namespace Xolvio\OpenApiGenerator\Data;

use Illuminate\Support\Arr;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Data as LaravelData;

class RequestBody extends Data
{
    public function __construct(
        public Content $content,
        public bool $required = true,
    ) {}

    public static function fromRoute(ReflectionMethod|ReflectionFunction $method): ?self
    {
        $type = self::getFirstOfClassType($method, LaravelData::class);

        if (! $type) {
            return null;
        }

        return new self(
            content: Content::fromReflection($type, $method),
        );
    }

    public static function getFirstOfClassType(ReflectionMethod|ReflectionFunction $method, string $class): ?ReflectionNamedType
    {
        $parameter = Arr::first(
            $method->getParameters(),
            static function (ReflectionParameter $parameter) use ($class) {
                $type = $parameter->getType();

                return $type instanceof ReflectionNamedType && is_a($type->getName(), $class, true);
            }
        );

        return $parameter ? $parameter->getType() : null;
    }
}
