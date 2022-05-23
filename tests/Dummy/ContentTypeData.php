<?php

namespace Xolvio\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;
use Xolvio\OpenApiGenerator\Attributes\CustomContentType;

#[CustomContentType(type: ['application/json', 'application/xml'])]
class ContentTypeData extends Data
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
