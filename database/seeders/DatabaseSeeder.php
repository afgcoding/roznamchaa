<?php

namespace Database\Seeders;

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
        // Primary tables first (no foreign keys)
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            UnitSeeder::class,
            WholesalerSeeder::class,
            CustomerSeeder::class,
            ExpenseSeeder::class,
        ]);

        // Tables with foreign keys
        $this->call([
            ProductSeeder::class,
            SaleSeeder::class, // also seeds sale_items + customer_ledgers
            WholesalerPaymentSeeder::class,
        ]);
    }
}
