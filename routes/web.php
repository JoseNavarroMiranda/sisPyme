<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\InventoryMovementsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SuppliersController;
use App\Models\Categories;
use App\Models\Customers;
use App\Models\Products;
use App\Models\Sales;
use App\Models\Suppliers;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::get('/dashboard', function () {
    $stats = [
        'sales_today' => Sales::whereDate('created_at', today())->where('status', 'completed')->count(),
        'revenue_today' => Sales::whereDate('created_at', today())->where('status', 'completed')->sum('total_amount'),
        'sales_total' => Sales::count(),
        'revenue_total' => Sales::where('status', 'completed')->sum('total_amount'),
        'products' => Products::count(),
        'low_stock' => Products::where('stock', '<=', 5)->count(),
        'customers' => Customers::count(),
        'categories' => Categories::count(),
        'suppliers' => Suppliers::count(),
    ];

    return view('dashboard', [
        'stats' => $stats,
        'recent_sales' => Sales::with('customer')->latest()->limit(5)->get(),
        'low_stock_products' => Products::with('category')->where('stock', '<=', 5)->orderBy('stock')->limit(5)->get(),
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('categories', CategoriesController::class);
    Route::resource('suppliers', SuppliersController::class);
    Route::resource('customers', CustomersController::class);
    Route::resource('products', ProductsController::class);
    Route::get('products-export-excel', [ProductsController::class, 'exportExcel'])->name('products.export-excel');
    Route::post('products-import-csv', [ProductsController::class, 'importCSV'])->name('products.import-csv');
    Route::resource('sales', SalesController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('sales/{sale}/cancel', [SalesController::class, 'cancel'])->name('sales.cancel');

    Route::resource('inventory-movements', InventoryMovementsController::class)->only(['index', 'store']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
