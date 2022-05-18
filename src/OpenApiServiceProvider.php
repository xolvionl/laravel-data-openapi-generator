<?php

namespace Xolvio\OpenApiGenerator;

use Illuminate\Support\ServiceProvider;
use Xolvio\OpenApiGenerator\Commands\GenerateOpenApiCommand;

class OpenApiServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/openapi-generator.php' => config_path('openapi-generator.php'),
        ], 'openapi-generator-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                GenerateOpenApiCommand::class,
            ]);
        }

        $this->loadRoutesFrom(__DIR__ . '/routes/routes.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'openapi-generator');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/openapi-generator.php',
            'openapi-generator'
        );
    }
}
