<?php

namespace App\Services;

use App\DTO\Shared\BaseFiltersDTO;
use App\DTO\Inventory\BulkDeleteProductsDTO;
use App\DTO\Inventory\BulkUpdateProductsDTO;
use App\DTO\Inventory\GetPaginatedProductsDTO;
use App\DTO\Inventory\UpdateOrCreateProductDTO;
use App\Events\Product\AddedStockEvent;
use App\Events\Product\BulkDeletedEvent;
use App\Events\Product\BulkUpdatedEvent;
use App\Events\Product\CreatedEvent;
use App\Events\Product\DeletedEvent;
use App\Events\Product\TookProductEvent;
use App\Events\Product\UpdatedEvent;
use App\Exceptions\StockTooLowException;
use App\Models\InventoryProducts;
use DB;

class InventoryService
{
    public function getPaginatedProducts(BaseFiltersDTO $dto)
    {
        $query = InventoryProducts::query();

        if (!empty($dto->search)) {
            $query->where('name', 'like', "%{$dto->search}%");
        }

        return $query->paginate($dto->perPage);
    }

    public function updateOrCreateProduct(UpdateOrCreateProductDTO $dto) {
        $product = InventoryProducts::updateOrCreate(
            ['id' => $dto->id],
            [
                'name' => $dto->name,
                'qty' => $dto->quantity,
                'should_alert' => $dto->shouldAlert,
                'alert_threshold' => $dto->alertThreshold,
                'note' => $dto->note,
                'restock_url' => $dto->restockUrl,
                'image_id' => $dto->imageId,
            ]
        );

        if ($product->wasRecentlyCreated) {
            CreatedEvent::dispatch($product);
        } else {
            UpdatedEvent::dispatch($product);
        }

        return $product;
    }

    public function deleteProduct(int $id)
    {
        $product = InventoryProducts::findOrFail($id);
        $product->delete();

        DeletedEvent::dispatch($product);

        return $product;
    }

    public function bulkDeleteProducts(BulkDeleteProductsDTO $dto)
    {
        $productsToDelete = InventoryProducts::find($dto->ids);
        $productsToDelete->each(fn ($product) => $product->delete());
        $deletedCount = $productsToDelete->count();

        BulkDeletedEvent::dispatch($productsToDelete);

        return $deletedCount;
    }

    public function bulkUpdateProducts(BulkUpdateProductsDTO $dto) {
        $products = InventoryProducts::whereIn('id', $dto->productIds)->get();

        foreach ($products as $product) {
            if ($dto->quantity !== null && $dto->quantity > 0) {
                $product->qty = $dto->quantity;
            }
            if ($dto->shouldAlert !== null && $dto->shouldAlert !== '') {
                $product->should_alert = $dto->shouldAlert === '1' || $dto->shouldAlert === 1 || $dto->shouldAlert === true;
                if ($product->should_alert && $dto->alertThreshold !== null && $dto->alertThreshold > 0) {
                    $product->alert_threshold = $dto->alertThreshold;
                }
            }
            $product->save();
        }

        BulkUpdatedEvent::dispatch($products, $dto->quantity ?? 0, $dto->alertThreshold ?? 0, $dto->shouldAlert ?? null);

        return $products;
    }

    public function addStock(int $id, int $quantity)
    {
        $product = InventoryProducts::findOrFail($id);
        $product->qty += $quantity;
        $product->save();

        AddedStockEvent::dispatch($product, $quantity);

        return $product;
    }

    public function takeProduct(int $id, int $quantity)
    {
        $product = InventoryProducts::findOrFail($id);
        if ($product->qty < $quantity) {
            return throw new StockTooLowException();
        }

        $product->qty -= $quantity;
        $product->save();

        TookProductEvent::dispatch($product, $quantity);

        return $product;
    }

    public function takeProducts(array $products)
    {
        DB::beginTransaction();
        try {
            foreach ($products as $product) {
                $this->takeProduct($product['id'], $product['quantity']);
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}