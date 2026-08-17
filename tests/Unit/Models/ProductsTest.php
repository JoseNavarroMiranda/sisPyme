<?php

namespace Tests\Unit\Models;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Suppliers;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            [
                'sku',
                'name',
                'purchase_price',
                'selling_price',
                'stock',
                'image_path',
                'category_id',
                'supplier_id',
            ],
            (new Products)->getFillable()
        );
    }

    public function test_uses_products_table(): void
    {
        $this->assertEquals('products', (new Products)->getTable());
    }

    public function test_belongs_to_category_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            (new Products)->category()
        );

        $this->assertInstanceOf(Categories::class, (new Products)->category()->getRelated());
        $this->assertEquals('category_id', (new Products)->category()->getForeignKeyName());
    }

    public function test_belongs_to_supplier_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\BelongsTo::class,
            (new Products)->supplier()
        );

        $this->assertInstanceOf(Suppliers::class, (new Products)->supplier()->getRelated());
    }

    public function test_fillable_excludes_id_and_timestamps(): void
    {
        $fillable = (new Products)->getFillable();

        $this->assertNotContains('id', $fillable);
        $this->assertNotContains('created_at', $fillable);
        $this->assertNotContains('updated_at', $fillable);
    }
}