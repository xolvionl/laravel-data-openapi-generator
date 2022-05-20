<?php 
use Illuminate\Routing\Route;
use Xolvio\OpenApiGenerator\Data\OpenApi;
use Xolvio\OpenApiGenerator\Data\RequestBody;

it('can create no request body', function () {
    foreach (['basic', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);
    
        expect(RequestBody::fromRoute($method)?->toArray())
            ->toBeNull();
    }
});

it('can create data request body', function () {
    foreach (['requestBasic', 'allCombined'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);
    
        expect(RequestBody::fromRoute($method)?->toArray())
            ->toBe([
                "content" => [
                  "application/json" =>[
                    "schema" => [
                      '$ref' => "#/components/schemas/RequestData"
                    ]
                  ]
                ]
            ]);

        expect(OpenApi::getSchemas())->toMatchArray(
            ["RequestData" => "Xolvio\OpenApiGenerator\Test\RequestData"]
        );
    }
});