<?php

namespace App\Http\Controllers;

use App\Models\InventoryProducts;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function listProducts(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'perPage' => 'nullable|integer|min:1|max:100',
        ]);

        $query = InventoryProducts::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        return $query->paginate($request->perPage ?? 20);
    }

    public function takeProduct(Request $request)
    {
        $data = $request->validate([
            'id' => 'required|exists:inventory_products,id',
            'qty' => 'required|integer|min:1',
        ]);

        $product = InventoryProducts::findOrFail($data['id']);
        if ($product->qty < $data['qty']) {
            return response()->json(['message' => 'Ikke nok på lager'], 422);
        }

        $product->qty -= $data['qty'];
        $product->save();

        return response()->json(['message' => "Produkt {$product->name} taget"], 200);
    }
}
