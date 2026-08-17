<?php

namespace Tests\Unit\Models;

use App\Models\inventory_Movements;
use App\Models\Products;
use Tests\TestCase;

class InventoryMovementsTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            ['type', 'quantity', 'description', 'product_id', 'user_id'],
            (new inventory_Movements)->getFillable()
        );
    }

    public function test_uses_inventory_double_underscore_table(): void
    {
        $this->assertEquals('inventory__movements', (new inventory_Movements)->getTable());
    }

    public function test_belongs_to_product_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            (new inventory_Movements)->product()
        );

        $this->assertInstanceOf(Products::class, (new inventory_Movements)->product()->getRelated());
        $this->assertEquals('product_id', (new inventory_Movements)->product()->getForeignKeyName());
    }
}