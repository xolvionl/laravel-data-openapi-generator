<?php

namespace Xolvio\OpenApiGenerator\Test;

use DateTime;
use DateTimeInterface;
use Spatie\LaravelData\Data;

class RequestData extends Data
{
    public function __construct(
        public int $integer,
        public ?int $nullable_integer,
        public string $string,
        public ?string $nullable_string,
        public bool $bool,
        public ?bool $nullable_bool,
        public float $float,
        public ?float $nullable_float,
        public ?RequestData $nullable_self,
        public ReturnData $other,
        public ?ReturnData $nullable_other,
        public DateTimeInterface $date,
        public ?DateTimeInterface $nullable_date,
    ) {}

    public static function create(): self
    {
        return new self(
            integer: 1,
            nullable_integer: null,
            string: 'string',
            nullable_string: null,
            bool: true,
            nullable_bool: null,
            float: 0.0,
            nullable_float: null,
            nullable_self: null,
            other: new ReturnData(),
            nullable_other: null,
            date: new DateTime(),
            nullable_date: null,
        );
    }
}
