<?php

namespace Tests\Unit\Models;

use App\Models\Products;
use App\Models\Sales;
use App\Models\salesDetails;
use Tests\TestCase;

class SalesDetailsTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            ['sales_id', 'product_id', 'quantity', 'price'],
            (new salesDetails)->getFillable()
        );
    }

    public function test_uses_sales_details_table(): void
    {
        $this->assertEquals('sales_details', (new salesDetails)->getTable());
    }

    public function test_belongs_to_sale_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            (new salesDetails)->sale()
        );

        $this->assertInstanceOf(Sales::class, (new salesDetails)->sale()->getRelated());
    }

    public function test_belongs_to_product_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            (new salesDetails)->product()
        );

        $this->assertInstanceOf(Products::class, (new salesDetails)->product()->getRelated());
    }
}