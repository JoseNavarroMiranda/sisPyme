<?php

namespace Tests\Unit\Models;

use App\Models\Products;
use App\Models\Suppliers;
use Tests\TestCase;

class SuppliersTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            ['name', 'contact_name', 'phone', 'email', 'suppliers_rfc'],
            (new Suppliers)->getFillable()
        );
    }

    public function test_uses_suppliers_table(): void
    {
        $this->assertEquals('suppliers', (new Suppliers)->getTable());
    }

    public function test_has_many_products_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            (new Suppliers)->products()
        );

        $this->assertInstanceOf(Products::class, (new Suppliers)->products()->getRelated());
        $this->assertEquals('supplier_id', (new Suppliers)->products()->getForeignKeyName());
    }
}