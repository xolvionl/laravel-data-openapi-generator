<?php

namespace Xolvio\OpenApiGenerator\Test;

class NotData
{
    public function __construct(
        public string $title,
        public string $version,
    ) {
    }

    public static function create(): self
    {
        return new self(
            title: 'title',
            version: 'version',
        );
    }
}
