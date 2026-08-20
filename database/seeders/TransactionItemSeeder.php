<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Database\Seeder;

class TransactionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::all()->each(function ($transaction) {
            TransactionItem::factory()->count(fake()->numberBetween(1, 5))->create([
                'transaction_id' => $transaction->id,
            ]);
        });
    }
}
