<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Models\Customers;
use App\Models\inventory_Movements;
use App\Models\Products;
use App\Models\Sales;
use App\Models\salesDetails;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Sales::with('customer', 'details.product')->latest()->get());
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $sale = DB::transaction(function () use ($data) {
                $total = 0;

                foreach ($data['items'] as $item) {
                    $product = Products::lockForUpdate()->findOrFail($item['product_id']);
                    if ($product->stock < $item['quantity']) {
                        abort(422, "Stock insuficiente para {$product->name}");
                    }
                    $total += $product->selling_price * $item['quantity'];
                }

                $sale = Sales::create([
                    'total_amount' => $total,
                    'status' => 'completed',
                    'user_id' => Auth::id(),
                    'customer_id' => $data['customer_id'],
                ]);

                foreach ($data['items'] as $item) {
                    $product = Products::lockForUpdate()->findOrFail($item['product_id']);

                    salesDetails::create([
                        'sales_id' => $sale->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'price' => $product->selling_price,
                    ]);

                    $product->decrement('stock', $item['quantity']);

                    inventory_Movements::create([
                        'type' => 'out',
                        'quantity' => $item['quantity'],
                        'description' => "Salida por venta #{$sale->id}",
                        'product_id' => $product->id,
                        'user_id' => Auth::id(),
                    ]);
                }

                return $sale;
            });

            return response()->json($sale->load('customer', 'details.product'), 201);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(Sales $sale): JsonResponse
    {
        $sale->load(['customer', 'details.product']);

        return response()->json($sale);
    }

    public function cancel(Sales $sale): JsonResponse
    {
        if ($sale->status !== 'completed') {
            return response()->json(['message' => 'Solo se pueden cancelar ventas completadas'], 422);
        }

        DB::transaction(function () use ($sale) {
            $sale->load('details.product');

            foreach ($sale->details as $detail) {
                $detail->product->increment('stock', $detail->quantity);

                inventory_Movements::create([
                    'type' => 'in',
                    'quantity' => $detail->quantity,
                    'description' => "Reversion por cancelacion de venta #{$sale->id}",
                    'product_id' => $detail->product_id,
                    'user_id' => Auth::id(),
                ]);
            }

            $sale->update(['status' => 'canceled']);
        });

        return response()->json(['message' => 'Venta cancelada']);
    }
}