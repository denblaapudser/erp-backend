<?php 

namespace App\DTO\Inventory;

use Spatie\LaravelData\Data;

class BulkDeleteProductsDTO extends Data{
    public function __construct(
        public array $ids,
    ) {}
}