<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Suppliers;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductsController extends Controller
{
    public function index()
    {
        $products = Products::with(['category', 'supplier'])->latest()->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Categories::where('is_active', true)->orderBy('name')->get();
        $suppliers = Suppliers::orderBy('name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        Products::create($data);

        return redirect()->route('products.index')->with('success', 'Producto creado');
    }

    public function show(Products $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Products $product)
    {
        $categories = Categories::where('is_active', true)->orderBy('name')->get();
        $suppliers = Suppliers::orderBy('name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(UpdateProductRequest $request, Products $product)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if (! empty($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Producto actualizado');
    }

    public function destroy(Products $product)
    {
        if (! empty($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Producto eliminado');
    }

    public function exportExcel(): StreamedResponse
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

    public function importCSV(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        if ($file === false) {
            return back()->with('error', 'No se pudo leer el archivo');
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

        return redirect()->route('products.index')->with('success', 'Importacion completada');
    }

    public function apiStock(string $sku)
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
}
