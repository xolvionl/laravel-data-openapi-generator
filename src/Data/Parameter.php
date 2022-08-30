<?php

namespace Xolvio\OpenApiGenerator\Data;

use Exception;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class Parameter extends Data
{
    public function __construct(
        public string $name,
        public string $in,
        public string $description,
        public bool $required,
        public Schema $schema,
    ) {
    }

    /**
     * @return null|DataCollection<int,Parameter>
     */
    public static function fromRoute(Route $route, ReflectionMethod|ReflectionFunction $method): ?DataCollection
    {
        /** @var string[] */
        $parameters = $route->parameterNames();

        if (0 === count($parameters)) {
            return null;
        }

        return Parameter::collection(array_map(
            fn (string $parameter) => Parameter::fromParameter($parameter, $method),
            $parameters,
        ));
    }

    public static function fromParameter(string $name, ReflectionMethod|ReflectionFunction $method): self
    {
        /** @var null|ReflectionParameter */
        $parameter = Arr::first(
            $method->getParameters(),
            fn (ReflectionParameter $parameter) => $parameter->getName() === $name,
        );

        if (! $parameter) {
            throw new Exception("Parameter {$name} not found in method {$method->getName()}");
        }

        return new self(
            name: $parameter->getName(),
            in: 'path',
            description: $parameter->getName(),
            required: ! $parameter->isOptional(),
            schema: Schema::fromParameterReflection($parameter),
        );
    }
}
