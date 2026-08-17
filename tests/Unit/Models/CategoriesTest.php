<?php

namespace Tests\Unit\Models;

use App\Models\Categories;
use App\Models\Products;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    public function test_fillable_attributes(): void
    {
        $this->assertEquals(
            ['name', 'description', 'is_active'],
            (new Categories)->getFillable()
        );
    }

    public function test_uses_categories_table(): void
    {
        $this->assertEquals('categories', (new Categories)->getTable());
    }

    public function test_has_many_products_relationship(): void
    {
        $this->assertInstanceOf(
            \Illuminate\Database\Eloquent\Relations\HasMany::class,
            (new Categories)->products()
        );

        $this->assertEquals('category_id', (new Categories)->products()->getForeignKeyName());
    }

    public function test_can_be_mass_assigned(): void
    {
        $category = new Categories([
            'name' => 'Electronica',
            'description' => 'Productos electronicos',
            'is_active' => true,
        ]);

        $this->assertEquals('Electronica', $category->name);
        $this->assertEquals('Productos electronicos', $category->description);
        $this->assertTrue($category->is_active);
    }

    public function test_relation_targets_products_model(): void
    {
        $this->assertInstanceOf(Products::class, (new Categories)->products()->getRelated());
    }
}