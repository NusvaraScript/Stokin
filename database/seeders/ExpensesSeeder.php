<?php

namespace Database\Seeders;

use App\Models\Expenses;
use Illuminate\Database\Seeder;

class ExpensesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Expenses::factory(15)->create();
    }
}
