<?php

namespace App\Http\Controllers;

use App\Models\inventory_Movements;
use App\Models\Products;
use App\Http\Requests\StoreInventoryMovementRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryMovementsController extends Controller
{
    public function index()
    {
        $movements = inventory_Movements::with('product')->latest()->get();
        $products = Products::orderBy('name')->get();

        return view('inventory_movements.index', compact('movements', 'products'));
    }

    public function create()
    {
        abort(404);
    }

    public function store(StoreInventoryMovementRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $product = Products::lockForUpdate()->findOrFail($data['product_id']);

            if ($data['type'] === 'out' && $product->stock < $data['quantity']) {
                abort(422, 'Stock insuficiente para el ajuste');
            }

            $data['type'] === 'in'
                ? $product->increment('stock', $data['quantity'])
                : $product->decrement('stock', $data['quantity']);

            inventory_Movements::create([
                'type' => $data['type'],
                'quantity' => $data['quantity'],
                'description' => $data['description'] ?? 'Ajuste manual de inventario',
                'product_id' => $product->id,
                'user_id' => Auth::id(),
            ]);
        });

        return redirect()->route('inventory-movements.index')->with('success', 'Ajuste registrado');
    }

    public function show(inventory_Movements $inventoryMovement)
    {
        abort(404);
    }

    public function edit(inventory_Movements $inventoryMovement)
    {
        abort(404);
    }

    public function update(\Illuminate\Http\Request $request, inventory_Movements $inventoryMovement)
    {
        abort(404);
    }

    public function destroy(inventory_Movements $inventoryMovement)
    {
        abort(404);
    }
}
