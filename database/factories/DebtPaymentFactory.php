<?php

namespace Database\Factories;

use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DebtPayment>
 */
class DebtPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory()->hutang(),
            'payment' => fake()->randomFloat(2, 10000, 100000),
            'note' => fake()->optional()->sentence(),
        ];
    }
}
