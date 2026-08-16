<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreInventoryMovementRequest;
use App\Models\inventory_Movements;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryMovementsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(inventory_Movements::with('product')->latest()->get());
    }

    public function store(StoreInventoryMovementRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $movement = DB::transaction(function () use ($data) {
                $product = Products::lockForUpdate()->findOrFail($data['product_id']);

                if ($data['type'] === 'out' && $product->stock < $data['quantity']) {
                    abort(422, 'Stock insuficiente para el ajuste');
                }

                $data['type'] === 'in'
                    ? $product->increment('stock', $data['quantity'])
                    : $product->decrement('stock', $data['quantity']);

                return inventory_Movements::create([
                    'type' => $data['type'],
                    'quantity' => $data['quantity'],
                    'description' => $data['description'] ?? 'Ajuste manual de inventario',
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                ]);
            });

            return response()->json($movement->load('product'), 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}