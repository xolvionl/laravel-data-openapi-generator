<?php

namespace Xolvio\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;

class ReturnData extends Data
{
    public function __construct(
        public string $title,
        public ?string $version,
    ) {
    }

    public static function create(mixed ...$parameters): self
    {
        return new self(
            title: 'title',
            version: null,
        );
    }
}
