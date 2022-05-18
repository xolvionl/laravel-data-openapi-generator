<?php

namespace Xolvio\OpenApiGenerator\Data;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Data;

class OpenApi extends Data
{
    /** @var array<string,class-string<Data>> */
    protected static array $schemas = [];

    public function __construct(
        public string $openapi,
        public Info $info,
        /** @var array<string,array<string,Operation>> */
        public array $paths,
    ) {
    }

    /**
     * @param class-string<Data> $schema
     */
    public static function addClassSchema(string $name, $schema): void
    {
        static::$schemas[$name] = $schema;
    }

    /**
     * @param array<string,array<string,Route>> $routes
     */
    public static function fromRoutes(array $routes, Command $command): self
    {
        /** @var array<string,array<string,Operation>> $paths */
        $paths = [];

        foreach ($routes as $uri => $uri_routes) {
            foreach ($uri_routes as $method => $route) {
                try {
                    $paths[$uri][$method] = Operation::fromRoute($route);
                } catch (\Throwable $th) {
                    $command->error("Failed to generate Operation from route {$method} {$route->getName()} {$uri}: {$th->getMessage()}");

                    Log::error($th);
                }
            }
        }

        return new self(
            openapi: config('openapi-generator.openapi'),
            info: Info::create(),
            paths: $paths,
        );
    }

    public function toArray(): array
    {
        // Double call to make sure all schemas are resolved
        $this->resolveSchemas();

        return array_merge(
            parent::toArray(),
            [
                'paths'                           => array_map(
                    fn (array $path)              => array_map(
                        fn (Operation $operation) => $operation->toArray(),
                        $path
                    ),
                    $this->paths
                ),
                'components' => [
                    'schemas'         => $this->resolveSchemas(),
                    'securitySchemes' => [
                        SecurityScheme::BEARER_SECURITY_SCHEME => [
                            'type'   => 'http',
                            'scheme' => 'bearer',
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * @return array<string,mixed>
     */
    protected function resolveSchemas(): array
    {
        return array_map(
            fn (string $schema) => Schema::fromDataClass($schema)->toArray(),
            static::$schemas
        );
    }
}
