<?php

namespace Xolvio\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;

class ReturnData extends Data
{
    public function __construct(
        public string $message = 'test',
    ) {}

    public static function create(mixed ...$parameters): self
    {
        return new self();
    }
}
