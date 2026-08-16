<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;

Route::get('products/{sku}/stock', [ProductsController::class, 'apiStock']);
