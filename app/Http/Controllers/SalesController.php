<?php

namespace App\Http\Controllers;

use App\Models\Customers;
use App\Models\inventory_Movements;
use App\Models\Products;
use App\Models\Sales;
use App\Models\salesDetails;
use App\Http\Requests\StoreSaleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        $sales = Sales::with('customer')->latest()->get();

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $customers = Customers::orderBy('first_name')->get();
        $products = Products::orderBy('name')->get();

        return view('sales.create', compact('customers', 'products'));
    }

    public function store(StoreSaleRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
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
        });

        return redirect()->route('sales.index')->with('success', 'Venta registrada');
    }

    public function show(Sales $sale)
    {
        $sale->load(['customer', 'details.product']);

        return view('sales.show', compact('sale'));
    }

    public function edit(Sales $sale)
    {
        abort(404);
    }

    public function update(Request $request, Sales $sale)
    {
        abort(404);
    }

    public function destroy(Sales $sale)
    {
        abort(404);
    }

    public function cancel(Sales $sale)
    {
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

        return redirect()->route('sales.index')->with('success', 'Venta cancelada');
    }
}
