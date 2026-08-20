<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\TransactionPhoto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionPhoto>
 */
class TransactionPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => Transaction::factory(),
            'image' => 'transactions/'.fake()->uuid().'.jpg',
        ];
    }
}
