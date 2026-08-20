<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin Stokin',
            'email' => 'admin@stokin.test',
        ]);

        $this->call([
            CustomerSeeder::class,
            ExpensesSeeder::class,
            TransactionSeeder::class,
            TransactionItemSeeder::class,
            TransactionPhotoSeeder::class,
            DebtPaymentSeeder::class,
        ]);
    }
}
