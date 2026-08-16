<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customers;
use Illuminate\Http\JsonResponse;

class CustomersController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Customers::withCount('sales')->latest()->get());
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = Customers::create($request->validated());

        return response()->json($customer, 201);
    }

    public function show(Customers $customer): JsonResponse
    {
        return response()->json($customer->load('sales'));
    }

    public function update(UpdateCustomerRequest $request, Customers $customer): JsonResponse
    {
        $customer->update($request->validated());

        return response()->json($customer);
    }

    public function destroy(Customers $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(['message' => 'Cliente eliminado']);
    }
}