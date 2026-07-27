<?php

namespace Database\Seeders;

use App\Models\Wholesaler;
use Illuminate\Database\Seeder;

class WholesalerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $wholesalers = [
            [
                'name' => 'حاجي رحیم - غله منډوي',
                'phone' => '0700123456',
                'address' => 'غله منډوي، کابل',
                'total_debt' => 15000.00,
            ],
            [
                'name' => 'خان تجارتي شرکت',
                'phone' => '0700987654',
                'address' => 'مندوي، کابل',
                'total_debt' => 8500.00,
            ],
            [
                'name' => 'ناصر د مشروباتو عرضه',
                'phone' => '0788123456',
                'address' => 'صنعتي ساحه، کابل',
                'total_debt' => 0,
            ],
        ];

        foreach ($wholesalers as $wholesaler) {
            Wholesaler::query()->create($wholesaler);
        }
    }
}
