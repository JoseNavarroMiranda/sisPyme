<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Products;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductsController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Products::with(['category', 'supplier'])->latest()->get());
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product = Products::create($data);

        return response()->json($product->load('category', 'supplier'), 201);
    }

    public function show(Products $product): JsonResponse
    {
        return response()->json($product->load('category', 'supplier'));
    }

    public function update(UpdateProductRequest $request, Products $product): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if (! empty($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json($product->load('category', 'supplier'));
    }

    public function destroy(Products $product): JsonResponse
    {
        if (! empty($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return response()->json(['message' => 'Producto eliminado']);
    }

    public function apiStock(string $sku): JsonResponse
    {
        $product = Products::where('sku', $sku)->first();

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        return response()->json([
            'sku' => $product->sku,
            'name' => $product->name,
            'stock' => $product->stock,
        ]);
    }

    public function export(): StreamedResponse
    {
        $fileName = 'products.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, ['sku', 'name', 'purchase_price', 'selling_price', 'stock', 'category', 'supplier']);

            Products::with(['category', 'supplier'])->orderBy('id')->chunk(200, function ($products) use ($stream) {
                foreach ($products as $product) {
                    fputcsv($stream, [
                        $product->sku,
                        $product->name,
                        $product->purchase_price,
                        $product->selling_price,
                        $product->stock,
                        $product->category?->name,
                        $product->supplier?->name,
                    ]);
                }
            });

            fclose($stream);
        };

        return response()->streamDownload($callback, $fileName, $headers);
    }

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        if ($file === false) {
            return response()->json(['message' => 'No se pudo leer el archivo'], 500);
        }

        $header = fgetcsv($file);

        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            if (! $data) {
                continue;
            }

            Products::updateOrCreate(
                ['sku' => $data['sku']],
                [
                    'name' => $data['name'],
                    'purchase_price' => $data['purchase_price'],
                    'selling_price' => $data['selling_price'],
                    'stock' => $data['stock'],
                    'category_id' => $data['category_id'],
                    'supplier_id' => $data['supplier_id'],
                ]
            );
        }

        fclose($file);

        return response()->json(['message' => 'Importacion completada']);
    }
}