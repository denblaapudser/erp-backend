<?php 

namespace App\DTO\Inventory;

use Spatie\LaravelData\Data;

class UpdateOrCreateProductDTO extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $imageId,
        public string $name,
        public int $quantity,
        public bool $shouldAlert,
        public int $alertThreshold,
        public ?string $note,
        public ?string $restockUrl,
    ) {}
}