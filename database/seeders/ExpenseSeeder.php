<?php

namespace Database\Seeders;

use App\Models\Expense;
use Illuminate\Database\Seeder;

class ExpenseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $expenses = [
            [
                'title' => 'د دوکان کرایه',
                'amount' => 8000.00,
                'date' => now()->startOfMonth()->toDateString(),
                'description' => 'د دوکان میاشتنۍ کرایه',
            ],
            [
                'title' => 'د برق بل',
                'amount' => 1200.00,
                'date' => now()->subDays(5)->toDateString(),
                'description' => 'د دوکان د برق لګښت',
            ],
            [
                'title' => 'د کاشیر معاش',
                'amount' => 5000.00,
                'date' => now()->startOfMonth()->toDateString(),
                'description' => 'د کاشیر میاشتنی معاش',
            ],
            [
                'title' => 'د پاکۍ توکي',
                'amount' => 350.00,
                'date' => now()->subDays(2)->toDateString(),
                'description' => null,
            ],
        ];

        foreach ($expenses as $expense) {
            Expense::query()->create($expense);
        }
    }
}
