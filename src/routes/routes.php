<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::name('api.openapi.')
    ->prefix('api')
    ->group(static function () {
        Route::get('openapi', static function () {
            return view('openapi-generator::swagger', ['json_url' => route('api.openapi.json')]);
        })->name('page');

        Route::get('openapi.json', static function () {
            return new JsonResponse(
                data: File::get(config('openapi-generator.path')),
                json: true,
            );
        })->name('json');
    });
