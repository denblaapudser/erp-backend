<?php

namespace App\Http\Controllers;

use App\DTO\Inventory\ActivityFiltersDTO;
use App\DTO\Inventory\BaseFiltersDTO;
use App\DTO\Inventory\BulkDeleteProductsDTO;
use App\DTO\Inventory\BulkUpdateProductsDTO;
use App\DTO\Inventory\UpdateOrCreateProductDTO;
use App\Http\Requests\Inventory\ActivitiesRequest;
use App\Http\Requests\Inventory\AddStockRequest;
use App\Http\Requests\Inventory\BulkDeleteProductsRequest;
use App\Http\Requests\Inventory\BulkUpdateProductsRequest;
use App\Http\Requests\Inventory\ListProductsRequest;
use App\Http\Requests\Inventory\TakeProductRequest;
use App\Http\Requests\Inventory\TakeProductsRequest;
use App\Http\Requests\Inventory\UpdateOrCreateProductRequest;
use App\Services\InventoryService;
use App\Services\ActivityService;

class InventoryController extends Controller
{
    public function listProducts(ListProductsRequest $request, InventoryService $inventoryService)
    {
        $paginatedProducts = $inventoryService->getPaginatedProducts(
            BaseFiltersDTO::from($request->validated())
        );
        return response()->json($paginatedProducts);
    }




    public function updateOrCreateProduct(UpdateOrCreateProductRequest $request, InventoryService $inventoryService)
    {
        $product = $inventoryService->updateOrCreateProduct(
            UpdateOrCreateProductDTO::from($request->validated())
        );
        return response()->json(['message' => "Produkt {$product->name} " . ($product->wasRecentlyCreated ? 'oprettet' : 'opdateret')], 200);
    }




    public function deleteProduct(int $id, InventoryService $inventoryService)
    {
        $deletedProduct = $inventoryService->deleteProduct($id);
        return response()->json(['message' => "Produkt {$deletedProduct->name} slettet"], 200);
    }




    public function bulkDeleteProducts(BulkDeleteProductsRequest $request, InventoryService $inventoryService)
    {
        $deletedCount = $inventoryService->bulkDeleteProducts(
            BulkDeleteProductsDTO::from($request->validated())
        );
        return response()->json(['message' => "{$deletedCount} produkter blev slettet"], 200);
    }




    public function bulkUpdateProducts(BulkUpdateProductsRequest $request, InventoryService $inventoryService)
    {
        $inventoryService->bulkUpdateProducts(
            BulkUpdateProductsDTO::from($request->validated())
        );
        return response()->json(['message' => 'Produkter opdateret'], 200);
    }




    public function addStock(AddStockRequest $request, int $id, InventoryService $inventoryService)
    {
        $product = $inventoryService->addStock($id, $request->quantity);
        return response()->json(['message' => "Produkt {$product->name} tilføjet"], 200);
    }




    public function takeProduct(TakeProductRequest $request, int $id, InventoryService $inventoryService)
    {
        try {
            $product = $inventoryService->takeProduct($id, $request->quantity);
            return response()->json(['message' => "Produkt {$product->name} taget"], 200);
        } catch (\App\Exceptions\StockTooLowException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Der skete en kritisk fejl under at tage produkt'], 500);
        }
    }




    public function takeProducts(TakeProductsRequest $request, InventoryService $inventoryService)
    {
        try {
            $inventoryService->takeProducts($request->products);
            return response()->json(['message' => count($request->products) . " produkter taget"], 200);
        } catch (\App\Exceptions\StockTooLowException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Der skete en kritisk fejl under at tage produkter'], 500);
        }
    }




    public function activities(int $id, ActivitiesRequest $request, ActivityService $activityService)
    {
        $activities = $activityService->getProductActivities($id, ActivityFiltersDTO::from($request->validated()));
        return response()->json($activities);
    }
}
