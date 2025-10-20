<?php

namespace Xolvio\OpenApiGenerator\Data;

use Closure;
use Exception;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Support\Transformation\TransformationContextFactory;
use Xolvio\OpenApiGenerator\Attributes\Tags;

class Operation extends Data
{
    public function __construct(
        public ?string $description,
        public ?RequestBody $requestBody,
        /** @var ?Collection<int,Parameter> */
        public ?Collection $parameters,
        /** @var Collection<string,Response> */
        public Collection $responses,
        /** @var ?Collection<int,SecurityScheme> */
        public ?Collection $security,
        /** @var ?Collection<int,string> */
        public ?Collection $tags
    ) {}

    public static function fromRoute(Route $route, string $method): self
    {
        $uses = $route->action['uses'];

        if (is_string($uses)) {
            $controller_class = new ReflectionClass($route->getController());
            $controller_function = $controller_class->getMethod($route->getActionMethod());

            echo $controller_class->name, "::", $controller_function->name, "\n";
        } elseif ($uses instanceof Closure) {
            $controller_class = null;
            $controller_function = new ReflectionFunction($uses);
        } else {
            throw new Exception('Unknown route uses');
        }

        $responses = Response::fromRoute($controller_function)->all();

        $security = SecurityScheme::fromRoute($route);
        if ($security->count() > 0) {
            $responses[HttpResponse::HTTP_UNAUTHORIZED] = Response::unauthorized($controller_function);
        }

        $description = null;
        $permissions = SecurityScheme::getPermissions($route);
        if (count($permissions) > 0) {
            $permissions_string = implode(', ', $permissions);

            $description = "Permissions needed: {$permissions_string}";

            $responses[HttpResponse::HTTP_FORBIDDEN] = Response::forbidden($controller_function);
        }

        $requestBody = RequestBody::fromRoute($controller_function);
        $params      = Parameter::fromRoute($route, $controller_function);
        if ('get' == $method && $requestBody) {
            $bodyParams  = Parameter::fromRequestBody($requestBody)->all();
            $params      = collect([...$params->all(), ...$bodyParams]);
            $requestBody = null;
        }

        return self::from([
            'description' => $description,
            'parameters'  => $params->count() > 0 ? $params : null,
            'requestBody' => $requestBody,
            'responses'   => $responses,
            'security'    => $security->count() > 0 ? $security : null,
            'method'      => $method,
            'tags'        => self::tagsFromReflection($controller_class, $controller_function),
        ]);
    }

    private static function tagsFromReflection(null|ReflectionClass|ReflectionMethod|ReflectionFunction ...$reflections): ?Collection
    {
        $tags = collect();
        foreach ($reflections as $reflection) {
            if (!$reflection) {
                continue;
            }
            $attributes = $reflection->getAttributes(Tags::class);
            foreach ($attributes as $attribute) {
                /** @var Tags $instance */
                $instance = $attribute->newInstance();
                $tags = $tags->merge($instance->tags);
            }
        }

        if ($tags->count() == 0) {
            return null;
        }
        return $tags;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function transform(
        null|TransformationContext|TransformationContextFactory $transformationContext = null,
    ): array {
        return array_filter(
            parent::transform($transformationContext),
            fn (mixed $value) => null !== $value,
        );
    }
}
