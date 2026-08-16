<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Suppliers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_endpoint_returns_product(): void
    {
        $category = Categories::create(['name' => 'C', 'description' => 'D', 'is_active' => true]);
        $supplier = Suppliers::create([
            'name' => 'S',
            'contact_name' => 'C',
            'phone' => '123',
            'email' => 's@example.com',
            'suppliers_rfc' => 'AAA010101AAA',
        ]);

        Products::create([
            'sku' => 'SKU-001',
            'name' => 'Laptop',
            'purchase_price' => 100,
            'selling_price' => 150,
            'stock' => 5,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $this->getJson('/api/products/SKU-001/stock')
            ->assertOk()
            ->assertJson(['sku' => 'SKU-001', 'stock' => 5]);
    }

    public function test_stock_endpoint_404_when_not_found(): void
    {
        $this->getJson('/api/products/NOPE/stock')->assertStatus(404);
    }
}