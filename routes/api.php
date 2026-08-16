<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoriesController;
use App\Http\Controllers\Api\CustomersController;
use App\Http\Controllers\Api\InventoryMovementsController;
use App\Http\Controllers\Api\ProductsController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\SuppliersController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('products/{sku}/stock', [ProductsController::class, 'apiStock']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('categories', CategoriesController::class);
    Route::apiResource('suppliers', SuppliersController::class);
    Route::apiResource('customers', CustomersController::class);

    Route::get('products/export', [ProductsController::class, 'export']);
    Route::post('products/import', [ProductsController::class, 'import']);
    Route::post('products', [ProductsController::class, 'store']);
    Route::put('products/{product}', [ProductsController::class, 'update']);
    Route::delete('products/{product}', [ProductsController::class, 'destroy']);
    Route::apiResource('products', ProductsController::class)->only(['index', 'show']);

    Route::apiResource('sales', SalesController::class)->only(['index', 'store', 'show']);
    Route::post('sales/{sale}/cancel', [SalesController::class, 'cancel']);

    Route::apiResource('inventory-movements', InventoryMovementsController::class)->only(['index', 'store']);
});