<?php

namespace App\DTO\Shared;

use Spatie\LaravelData\Data;

class BaseFiltersDTO extends Data
{
    public function __construct(
        public ?string $search,
        public int $perPage = 20,
    ) {}
}