<?php

namespace Xolvio\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;

class RequestData extends Data
{
    public function __construct(
        public string $title,
        public ?string $version,
    ) {
    }

    public static function create(): self
    {
        return new self(
            title: 'title',
            version: null,
        );
    }
}
