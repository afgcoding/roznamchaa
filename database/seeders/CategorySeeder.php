<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'روغنیات', 'description' => 'د پخلی غوړي او غوړ'],
            ['name' => 'حبوبات', 'description' => 'وريجې، غنم، لوبيا او نورې دانې'],
            ['name' => 'مشروبات', 'description' => 'سافټ ډرینک، جوس او اوبه'],
            ['name' => 'بسکټ', 'description' => 'بسکټ او سپک خوړل'],
            ['name' => 'شامپو', 'description' => 'د ویښتانو پاملرنې توکي'],
            ['name' => 'لبنیات', 'description' => 'شیدې، مستې او پنیر'],
        ];

        foreach ($categories as $category) {
            Category::query()->create($category);
        }
    }
}
