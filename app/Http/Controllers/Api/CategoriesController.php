<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Categories;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class CategoriesController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Categories::withCount('products')->latest()->get());
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        try {
            $category = Categories::create($request->validated());

            return response()->json($category, 201);
        } catch (QueryException) {
            return response()->json(['message' => 'Error al crear la categoria'], 500);
        }
    }

    public function show(Categories $category): JsonResponse
    {
        return response()->json($category->load('products'));
    }

    public function update(UpdateCategoryRequest $request, Categories $category): JsonResponse
    {
        $category->update($request->validated());

        return response()->json($category);
    }

    public function destroy(Categories $category): JsonResponse
    {
        $category->delete();

        return response()->json(['message' => 'Categoria eliminada']);
    }
}