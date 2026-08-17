<?php

namespace Tests\Feature;

use App\Models\Categories;
use App\Models\Customers;
use App\Models\inventory_Movements;
use App\Models\Products;
use App\Models\Sales;
use App\Models\salesDetails;
use App\Models\Suppliers;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FactoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_factory_persists(): void
    {
        $category = Categories::factory()->create();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertCount(1, Categories::all());
    }

    public function test_product_factory_creates_related_category_and_supplier(): void
    {
        $product = Products::factory()->create();

        $this->assertNotNull($product->category);
        $this->assertNotNull($product->supplier);
        $this->assertNull($product->image_path);
    }

    public function test_product_can_be_low_stock_or_out_of_stock(): void
    {
        $low = Products::factory()->lowStock()->create();
        $out = Products::factory()->outOfStock()->create();

        $this->assertLessThanOrEqual(5, $low->stock);
        $this->assertGreaterThan(0, $low->stock);
        $this->assertEquals(0, $out->stock);
    }

    public function test_sale_factory_creates_user_and_customer(): void
    {
        $sale = Sales::factory()->create();

        $this->assertNotNull($sale->customer);
        $this->assertNotNull($sale->user);
        $this->assertEquals('completed', $sale->status);
    }

    public function test_sale_can_be_cancelled_via_factory_state(): void
    {
        $this->assertEquals('canceled', Sales::factory()->cancelled()->create()->status);
    }

    public function test_sales_details_factory_wires_full_chain(): void
    {
        $sale = Sales::factory()->for(Customers::factory(), 'customer')->create();

        $detail = salesDetails::factory()
            ->for($sale, 'sale')
            ->for(Products::factory(), 'product')
            ->create(['quantity' => 3, 'price' => 25.50]);

        $this->assertEquals(3, $detail->quantity);
        $this->assertEquals(25.50, $detail->price);
        $this->assertNotNull($detail->sale);
        $this->assertNotNull($detail->product);
        $this->assertNotNull($detail->sale->customer);
    }

    public function test_sale_total_matches_its_details(): void
    {
        $sale = Sales::factory()->create(['total_amount' => 0]);

        salesDetails::factory()->count(2)
            ->for($sale, 'sale')
            ->for(Products::factory(), 'product')
            ->create([
                'quantity' => 2,
                'price' => 50,
            ]);

        $expected = $sale->details->sum(fn ($d) => $d->price * $d->quantity);

        $this->assertEquals(200.0, $expected);
    }

    public function test_inventory_movement_factory_persists(): void
    {
        $movement = inventory_Movements::factory()->create(['type' => 'in']);

        $this->assertDatabaseHas('inventory__movements', ['id' => $movement->id, 'type' => 'in']);
        $this->assertNotNull($movement->product);
    }

    public function test_factories_respect_unique_fields(): void
    {
        $agent1 = Suppliers::factory()->create();
        $agent2 = Suppliers::factory()->create();

        $this->assertNotEquals($agent1->email, $agent2->email);
        $this->assertNotEquals($agent1->suppliers_rfc, $agent2->suppliers_rfc);
    }
}