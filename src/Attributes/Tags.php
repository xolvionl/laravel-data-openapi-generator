<?php

namespace Xolvio\OpenApiGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Tags
{
    public function __construct(
        /** @var string[] */
        public array $tags
    ) {}
}
