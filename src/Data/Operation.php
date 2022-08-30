<?php

namespace Xolvio\OpenApiGenerator\Data;

use Closure;
use Exception;
use Illuminate\Routing\Route;
use ReflectionFunction;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;

class Operation extends Data
{
    public function __construct(
        /** @var null|DataCollection<int,Parameter> */
        #[DataCollectionOf(Parameter::class)]
        public ?DataCollection $parameters,
        public ?RequestBody $requestBody,
        public DefaultResponse $responses,
        /** @var null|DataCollection<int,SecurityScheme> */
        #[DataCollectionOf(SecurityScheme::class)]
        public ?DataCollection $security,
    ) {
    }

    public static function fromRoute(Route $route): self
    {
        $uses = $route->action['uses'];

        if (is_string($uses)) {
            $controller_function = (new \ReflectionClass($route->getController()))
                ->getMethod($route->getActionMethod());
        } elseif ($uses instanceof Closure) {
            $controller_function = new ReflectionFunction($uses);
        } else {
            throw new Exception('Unknown route uses');
        }

        return new self(
            parameters: Parameter::fromRoute($route, $controller_function),
            requestBody: RequestBody::fromRoute($controller_function),
            responses: new DefaultResponse(Response::fromRoute($controller_function)),
            security: SecurityScheme::fromRoute($route),
        );
    }

    /**
     * @return array<int|string,mixed>
     */
    public function transform(
        bool $transformValues = true,
        WrapExecutionType $wrapExecutionType = WrapExecutionType::Disabled,
    ): array {
        return array_filter(
            parent::transform($transformValues, $wrapExecutionType),
            fn (mixed $value) => null !== $value,
        );
    }
}
