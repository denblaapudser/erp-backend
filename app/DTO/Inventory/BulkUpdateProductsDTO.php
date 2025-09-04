<?php

namespace App\DTO\Inventory;

use Spatie\LaravelData\Data;

class BulkUpdateProductsDTO extends Data
{
    public function __construct(
        public array $productIds,
        public ?int $quantity,
        public ?int $alertThreshold,
        public ?bool $shouldAlert,
    ) {}
}