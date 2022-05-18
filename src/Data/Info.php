<?php

namespace Xolvio\OpenApiGenerator\Data;

use Spatie\LaravelData\Data;

class Info extends Data
{
    public function __construct(
        public string $title,
        public string $version,
    ) {
    }

    public static function create(): self
    {
        return new self(
            title: config('app.name'),
            version: config('app.version', '1.0.0'),
        );
    }
}
