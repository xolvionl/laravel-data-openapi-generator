<?php

namespace Xolvio\OpenApiGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route as FacadeRoute;
use Xolvio\OpenApiGenerator\Data\OpenApi;

class GenerateOpenApiCommand extends Command
{
    protected $signature   = 'openapi:generate';
    protected $description = 'Generates the OpenAPI documentation';

    public function handle(): int
    {
        $openapi = OpenApi::fromRoutes($this->getRoutes(), $this);

        $location  = config('openapi-generator.path');
        $directory = dirname($location);

        if (! File::isDirectory($directory)) {
            File::makeDirectory(
                path: dirname($location),
                recursive: true,
            );
        }

        File::put(
            $location,
            $openapi->toJson(JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
        );

        $this->info("OpenAPI documentation generated at {$location}");

        return Command::SUCCESS;
    }

    /**
     * @return array<string,array<string,Route>>
     */
    protected function getRoutes(): array
    {
        /** @var array<string,array<string,Route>> */
        $routes = [];

        /** @var array<int,Route> */
        $initial_routes = array_values(array_filter(
            FacadeRoute::getRoutes()->getRoutes(),
            function (Route $route) {
                $first_prefix = explode('/', $route->getPrefix() ?? '')[0];

                return in_array($first_prefix, config('openapi-generator.included_route_prefixes', []), true)
                && ! $this->strStartsWith($route->getName() ?? '', config('openapi-generator.ignored_route_names', []));
            },
        ));

        foreach ($initial_routes as $route) {
            $uri = '/' . $route->uri;

            if (! key_exists($uri, $routes)) {
                $routes[$uri] = [];
            }

            /** @var string $method */
            foreach ($route->methods as $method) {
                $method = strtolower($method);
                if (in_array($method, config('openapi-generator.ignored_methods', []), true)) {
                    continue;
                }

                $this->info("Found route {$method} {$route->getName()} {$uri}");

                $routes[$uri][$method] = $route;
            }
        }

        return $routes;
    }

    /**
     * @param string|string[] $needles
     */
    protected function strStartsWith(string $haystack, string|array $needles): bool
    {
        foreach ((array) $needles as $needle) {
            if ('' !== (string) $needle && str_starts_with($haystack, $needle)) {
                return true;
            }
        }

        return false;
    }
}
