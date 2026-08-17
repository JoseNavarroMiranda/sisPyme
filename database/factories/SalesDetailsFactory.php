<?php

namespace Database\Factories;

use App\Models\Products;
use App\Models\Sales;
use App\Models\salesDetails;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\salesDetails>
 */
class SalesDetailsFactory extends Factory
{
    protected $model = salesDetails::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quantity' => fake()->numberBetween(1, 10),
            'price' => fake()->randomFloat(2, 5, 500),
            'sales_id' => Sales::factory(),
            'product_id' => Products::factory(),
        ];
    }
}