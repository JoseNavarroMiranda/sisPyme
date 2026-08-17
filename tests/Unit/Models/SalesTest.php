<?php

namespace Tests\Unit\Models;

use App\Models\Customers;
use App\Models\Sales;
use App\Models\salesDetails;
use Tests\TestCase;

class SalesTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            ['total_amount', 'status', 'user_id', 'customer_id'],
            (new Sales)->getFillable()
        );
    }

    public function test_uses_sales_table(): void
    {
        $this->assertEquals('sales', (new Sales)->getTable());
    }

    public function test_belongs_to_customer_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            (new Sales)->customer()
        );

        $this->assertInstanceOf(Customers::class, (new Sales)->customer()->getRelated());
    }

    public function test_has_many_details_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            (new Sales)->details()
        );

        $this->assertInstanceOf(salesDetails::class, (new Sales)->details()->getRelated());
    }
}