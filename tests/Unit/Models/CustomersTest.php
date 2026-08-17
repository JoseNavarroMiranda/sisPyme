<?php

namespace Tests\Unit\Models;

use App\Models\Customers;
use App\Models\Sales;
use Tests\TestCase;

class CustomersTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            ['first_name', 'last_name', 'email', 'phone', 'rfc'],
            (new Customers)->getFillable()
        );
    }

    public function test_uses_customers_table(): void
    {
        $this->assertEquals('customers', (new Customers)->getTable());
    }

    public function test_has_many_sales_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            (new Customers)->sales()
        );

        $this->assertInstanceOf(Sales::class, (new Customers)->sales()->getRelated());
        $this->assertEquals('customer_id', (new Customers)->sales()->getForeignKeyName());
    }
}