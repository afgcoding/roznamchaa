<?php

namespace Database\Seeders;

use App\Models\Wholesaler;
use App\Models\WholesalerPayment;
use Illuminate\Database\Seeder;

class WholesalerPaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hajiRahim = Wholesaler::query()->where('name', 'حاجي رحیم - غله منډوي')->first();
        $khanTrading = Wholesaler::query()->where('name', 'خان تجارتي شرکت')->first();

        WholesalerPayment::query()->create([
            'wholesaler_id' => $hajiRahim->id,
            'amount' => 5000.00,
            'date' => now()->subDays(10)->toDateString(),
            'note' => 'د وریجو او غوړیو د اخیستلو جزوي تادیه',
        ]);

        WholesalerPayment::query()->create([
            'wholesaler_id' => $hajiRahim->id,
            'amount' => 3000.00,
            'date' => now()->subDays(3)->toDateString(),
            'note' => 'په مندوي کې نقد تادیه',
        ]);

        WholesalerPayment::query()->create([
            'wholesaler_id' => $khanTrading->id,
            'amount' => 2500.00,
            'date' => now()->subDays(7)->toDateString(),
            'note' => null,
        ]);
    }
}
