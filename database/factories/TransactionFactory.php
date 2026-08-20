<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $total = fake()->randomFloat(2, 10000, 500000);
        $status = fake()->randomElement(['Lunas', 'Hutang']);

        return [
            'customer_id' => Customer::factory(),
            'total' => $total,
            'status' => $status,
            'amount_paid' => $status === 'Lunas' ? $total : fake()->randomFloat(2, 0, $total),
            'remaining_debt' => $status === 'Lunas' ? 0 : fake()->randomFloat(2, 1000, $total),
            'note' => fake()->optional()->sentence(),
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Lunas',
            'amount_paid' => $attributes['total'] ?? 0,
            'remaining_debt' => 0,
        ]);
    }

    public function hutang(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'Hutang',
            'amount_paid' => fake()->randomFloat(2, 0, ($attributes['total'] ?? 100000) / 2),
            'remaining_debt' => fake()->randomFloat(2, 1000, ($attributes['total'] ?? 100000) / 2),
        ]);
    }
}
