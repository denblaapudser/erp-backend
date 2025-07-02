<?php

namespace App\Services;

use App\Events\Product\AddedStockEvent;
use App\Events\Product\BulkDeletedEvent;
use App\Events\Product\BulkUpdatedEvent;
use App\Events\Product\CreatedEvent;
use App\Events\Product\DeletedEvent;
use App\Events\Product\TookProductEvent;
use App\Events\Product\UpdatedEvent;
use App\Exceptions\StockTooLowException;
use App\Models\InventoryProducts;

class InventoryService
{
    public function getPaginatedProducts(string $search = null, int $perPage = 20)
    {
        $query = InventoryProducts::query();

        if (!empty($search)) {
            $query->where('name', 'like', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function updateOrCreateProduct(
        ?int $id,
        string $name,
        int $quantity,
        bool $shouldAlert,
        int $alertThreshold,
        ?string $note = null,
        ?string $restockUrl = null,
        ?int $imageId = null
    ) {
        $product = InventoryProducts::updateOrCreate(
            ['id' => $id],
            [
                'name' => $name,
                'qty' => $quantity,
                'should_alert' => $shouldAlert,
                'alert_threshold' => $alertThreshold,
                'note' => $note,
                'restock_url' => $restockUrl,
                'image_id' => $imageId,
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

    public function bulkDeleteProducts(array $ids)
    {
        $productsToDelete = InventoryProducts::find($ids);
        $productsToDelete->each(fn ($product) => $product->delete());
        $deletedCount = $productsToDelete->count();

        BulkDeletedEvent::dispatch($productsToDelete);

        return $deletedCount;
    }

    public function bulkUpdateProducts(
        array $productIds,
        ?int $quantity = null,
        ?int $alertThreshold = null,
        $shouldAlert = null
    ) {
        $products = InventoryProducts::whereIn('id', $productIds)->get();

        foreach ($products as $product) {
            if ($quantity !== null && $quantity > 0) {
                $product->qty = $quantity;
            }
            if ($shouldAlert !== null && $shouldAlert !== '') {
                $product->should_alert = $shouldAlert === '1' || $shouldAlert === 1 || $shouldAlert === true;
                if ($product->should_alert && $alertThreshold !== null && $alertThreshold > 0) {
                    $product->alert_threshold = $alertThreshold;
                }
            }
            $product->save();
        }

        BulkUpdatedEvent::dispatch($products, $quantity ?? 0, $alertThreshold ?? 0, $shouldAlert ?? null);

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
}