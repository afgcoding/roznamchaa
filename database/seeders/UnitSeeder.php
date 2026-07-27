<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * د واحد نوم په پښتو، short_name په انګلیسي
     */
    public function run(): void
    {
        $units = [
            ['name' => 'کارتن', 'short_name' => 'ctn'],
            ['name' => 'بوجۍ', 'short_name' => 'bag'],
            ['name' => 'بسته', 'short_name' => 'pkg'],
            ['name' => 'قطۍ', 'short_name' => 'box'],
            ['name' => 'دانه', 'short_name' => 'pcs'],
            ['name' => 'کیلو', 'short_name' => 'kg'],
            ['name' => 'لیټر', 'short_name' => 'ltr'],
        ];

        foreach ($units as $unit) {
            Unit::query()->create($unit);
        }
    }
}
