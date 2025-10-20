<?php

namespace Xolvio\OpenApiGenerator\Data;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
use RuntimeException;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Xolvio\OpenApiGenerator\Attributes\HttpResponseStatus;

class Response extends Data
{
    public function __construct(
        public string $description,
        public Content $content,
    ) {}

    /** @return Collection<int, static> */
    public static function fromRoute(ReflectionMethod|ReflectionFunction $method): Collection
    {
        $type  = $method->getReturnType();
        $types = $type instanceof ReflectionUnionType ? $type->getTypes() : [$type];

        return collect($types)->mapWithKeys(function (ReflectionType $type) use ($method) {
            if (! $type instanceof ReflectionNamedType) {
                throw new RuntimeException('Unsupported return type: ' . $type->getName());
            }

            return [
                self::statusCodeFromType($type) => new self(
                    description: $method->getName(),
                    content: Content::fromReflection($type, $method),
                ),
            ];
        });
    }

    public static function statusCodeFromType(ReflectionNamedType $type): int
    {
        if ($type->isBuiltin()) {
            return HttpResponse::HTTP_OK;
        }

        $class      = new ReflectionClass($type->getName());
        $attributes = $class->getAttributes(HttpResponseStatus::class);

        return count($attributes) > 0 ? $attributes[0]->getArguments()['status'] : HttpResponse::HTTP_OK;
    }

    public static function unauthorized(ReflectionMethod|ReflectionFunction $method): self
    {
        return new self(
            description: 'Unauthorized',
            content: Content::fromClass(config('openapi-generator.error_scheme_class'), $method),
        );
    }

    public static function forbidden(ReflectionMethod|ReflectionFunction $method): self
    {
        return new self(
            description: 'Forbidden',
            content: Content::fromClass(config('openapi-generator.error_scheme_class'), $method),
        );
    }
}
