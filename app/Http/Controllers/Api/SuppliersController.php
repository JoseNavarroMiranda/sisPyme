<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Suppliers;
use Illuminate\Http\JsonResponse;

class SuppliersController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Suppliers::withCount('products')->latest()->get());
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = Suppliers::create($request->validated());

        return response()->json($supplier, 201);
    }

    public function show(Suppliers $supplier): JsonResponse
    {
        return response()->json($supplier->load('products'));
    }

    public function update(UpdateSupplierRequest $request, Suppliers $supplier): JsonResponse
    {
        $supplier->update($request->validated());

        return response()->json($supplier);
    }

    public function destroy(Suppliers $supplier): JsonResponse
    {
        $supplier->delete();

        return response()->json(['message' => 'Proveedor eliminado']);
    }
}