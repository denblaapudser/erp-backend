<?php

namespace App\Http\Controllers;

use App\Models\InventoryProducts;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function warnings(Request $request)
    {
        $productsWithLowStock = InventoryProducts::where(function ($query) {
            $query->where(function ($q) {
            $q->where('should_alert', true)
              ->whereColumn('alert_threshold', '>', 'qty');
            })->orWhere(function ($q) {
            $q->where('should_alert', false)
              ->where('qty', '<', 10);
            });
        })->get();
        $lowStockCount = $productsWithLowStock->count();

        $warnings = [];

        if ($lowStockCount > 0) {
            $warnings[] = [
                'label' => "Lav lagerbeholdning for {$lowStockCount} produkter",
                'data' => $productsWithLowStock
            ];
        };

        return response()->json($warnings);
    }

    public function totalProducts()
    {
        return response()->json(InventoryProducts::count());
    }

    public function totalUsers()
    {
        return response()->json(User::count());
    }
}
