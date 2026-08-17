<?php

namespace Database\Factories;

use App\Models\inventory_Movements;
use App\Models\Products;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\inventory_Movements>
 */
class InventoryMovementsFactory extends Factory
{
    protected $model = inventory_Movements::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['in', 'out']),
            'quantity' => fake()->numberBetween(1, 50),
            'description' => fake()->sentence(),
            'product_id' => Products::factory(),
            'user_id' => User::factory(),
        ];
    }
}