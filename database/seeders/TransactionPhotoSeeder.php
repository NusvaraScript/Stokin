<?php

namespace Database\Seeders;

use App\Models\Transaction;
use App\Models\TransactionPhoto;
use Illuminate\Database\Seeder;

class TransactionPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transaction::all()->each(function ($transaction) {
            TransactionPhoto::factory()->count(fake()->numberBetween(0, 3))->create([
                'transaction_id' => $transaction->id,
            ]);
        });
    }
}
