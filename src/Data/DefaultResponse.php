<?php

namespace Xolvio\OpenApiGenerator\Data;

use Spatie\LaravelData\Data;

class DefaultResponse extends Data
{
    public function __construct(
        public Response $default,
    ) {
    }
}
