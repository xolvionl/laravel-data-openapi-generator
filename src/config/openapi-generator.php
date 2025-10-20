<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAPI version
    |--------------------------------------------------------------------------
    |
    | The version of the OpenAPI specification that you want to generate.
    |
    */
    'openapi' => '3.0.2',

    /*
    |--------------------------------------------------------------------------
    | App version
    |--------------------------------------------------------------------------
    |
    | The version of the OpenAPI specification that you want to generate.
    |
    */
    'version' => '1.0.0',

    /*
    |--------------------------------------------------------------------------
    | OpenAPI app name
    |--------------------------------------------------------------------------
    |
    | The name used in the OpenAPI specification.
    |
    */
    'name' => 'OpenAPI',

    /*
    |--------------------------------------------------------------------------
    | OpenAPI file location
    |--------------------------------------------------------------------------
    |
    | The location where the OpenAPI file should be generated.
    |
    */
    'path' => resource_path('api/openapi.json'),

    /*
    |--------------------------------------------------------------------------
    | Ignored route methods
    |--------------------------------------------------------------------------
    |
    | The methods that should be ignored when generating the OpenAPI file.
    |
    */
    'ignored_methods' => [
        'head',
        'options',
    ],

    /*
    |--------------------------------------------------------------------------
    | Included routes
    |--------------------------------------------------------------------------
    |
    | The routes that should be included when generating the OpenAPI file.
    | Uses the Route::getPrefix method to determine.
    |
    */
    'included_route_prefixes' => [
        'api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded routes
    |--------------------------------------------------------------------------
    |
    | The routes that should be excluded when generating the OpenAPI file.
    | Uses a str_starts_with comparison with the Route::getName method to determine.
    |
    */
    'ignored_route_names' => [
        'api.openapi.',
        'api.not_found',
    ],

    'security_middlewares' => [
        \Xolvio\OpenApiGenerator\Data\SecurityScheme::BEARER_SECURITY_SCHEME => ['auth:sanctum'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error scheme class
    |--------------------------------------------------------------------------
    |
    | Data class used to create the error scheme.
    |
    */
    'error_scheme_class' => \Xolvio\OpenApiGenerator\Data\Error::class,
];
