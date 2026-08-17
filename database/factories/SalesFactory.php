<?php

namespace Database\Factories;

use App\Models\Customers;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sales>
 */
class SalesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'total_amount' => fake()->randomFloat(2, 10, 1000),
            'status' => 'completed',
            'user_id' => User::factory(),
            'customer_id' => Customers::factory(),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => 'canceled']);
    }
}