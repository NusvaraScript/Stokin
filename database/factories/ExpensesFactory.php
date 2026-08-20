<?php

namespace Database\Factories;

use App\Models\Expenses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expenses>
 */
class ExpensesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['Operasional', 'Stok', 'Lainnya']),
            'description' => fake()->sentence(),
            'amount' => fake()->randomFloat(2, 1000, 50000),
        ];
    }
}
