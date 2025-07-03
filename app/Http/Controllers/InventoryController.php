<?php

namespace App\Http\Controllers;

use App\Services\InventoryService;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function listProducts(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'perPage' => 'nullable|integer|min:1|max:100',
        ]);

        $paginatedProducts = $inventoryService->getPaginatedProducts(
            $request->input('search'),
            $request->input('perPage', 20)
        );

        return response()->json($paginatedProducts);
    }

    public function updateOrCreateProduct(Request $request, InventoryService $inventoryService)
    {
        $request->validate([
            'id' => 'sometimes|exists:inventory_products,id',
            'imageId' => 'nullable|exists:images,id',
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'shouldAlert' => 'required|boolean',
            'alertThreshold' => 'required|integer|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        $product = $inventoryService->updateOrCreateProduct(
            $request->input('id'),
            $request->input('name'),
            $request->input('quantity'),
            $request->input('shouldAlert'),
            $request->input('alertThreshold'),
            $request->input('note', null),
            $request->input('restockUrl', null),
            $request->input('imageId', null)
        );

        return response()->json(['message' => "Produkt {$product->name} " . ($product->wasRecentlyCreated ? 'oprettet' : 'opdateret')], 200);
    }

    public function deleteProduct(int $id, InventoryService $inventoryService)
    {
        $deletedProduct = $inventoryService->deleteProduct($id);
        return response()->json(['message' => "Produkt {$deletedProduct->name} slettet"], 200);
    }

    public function bulkDeleteProducts(Request $request, InventoryService $inventoryService)
    {
        $data = $request->validate([
            'ids.*' => 'exists:inventory_products,id',
        ]);
        $deletedCount = $inventoryService->bulkDeleteProducts($data['ids']);

        return response()->json(['message' => "{$deletedCount} produkter blev slettet"], 200);
    }

    public function bulkUpdateProducts(Request $request, InventoryService $inventoryService)
    {
        $data = (object) $request->validate([
            'productIds' => 'required|array',
            'productIds.*' => 'exists:inventory_products,id',
            'quantity' => 'nullable|integer|min:0',
            'alertThreshold' => 'nullable|integer|min:0',
            'shouldAlert' => 'nullable|string',
        ]);

        $inventoryService->bulkUpdateProducts(
            $data->productIds,
            $data->quantity ?? 0,
            $data->alertThreshold ?? 0,
            $data->shouldAlert ?? null
        );
    
        return response()->json(['message' => 'Produkter opdateret'], 200);
    }

    public function addStock(Request $request, int $id, InventoryService $inventoryService)
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        $product = $inventoryService->addStock($id, $data['quantity']);

        return response()->json(['message' => "Produkt {$product->name} tilføjet"], 200);
    }

    public function takeProduct(Request $request, int $id, InventoryService $inventoryService)
    {
        try {
            $data = $request->validate([
                'quantity' => 'required|integer|min:1',
            ]);
            $product = $inventoryService->takeProduct($id, $data['quantity']);

            return response()->json(['message' => "Produkt {$product->name} taget"], 200);
        } catch (\App\Exceptions\StockTooLowException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode() ?: 422);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Der skete en kritisk fejl under at tage produkt'], 500);
        }
       
    }

    public function activities(int $id, Request $request, ActivityService $activityService)
    {
        $filters = (object) $request->validate([
            'type' => 'nullable|string',
            'search' => 'nullable|string|max:255',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'perPage' => 'nullable|integer',
        ]);
        $activities = $activityService->getProductActivities($id, $filters);

        return response()->json($activities);
    }
}
