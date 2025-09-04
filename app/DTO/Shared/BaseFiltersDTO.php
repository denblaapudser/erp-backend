<?php

namespace App\DTO\Inventory;

use Spatie\LaravelData\Data;

class BaseFiltersDTO extends Data
{
    public function __construct(
        public ?string $search,
        public int $perPage = 20,
    ) {}
}