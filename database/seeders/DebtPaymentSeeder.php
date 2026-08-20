<?php

namespace Database\Seeders;

use App\Models\DebtPayment;
use App\Models\Transaction;
use Illuminate\Database\Seeder;

class DebtPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::where('status', 'Hutang')->each(function ($transaction) {
            DebtPayment::factory()->count(fake()->numberBetween(1, 3))->create([
                'transaction_id' => $transaction->id,
            ]);
        });
    }
}
