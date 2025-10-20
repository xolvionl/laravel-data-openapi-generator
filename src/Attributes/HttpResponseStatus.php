<?php

namespace Xolvio\OpenApiGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class HttpResponseStatus
{
    public function __construct(
        public int $status
    ) {}
}
