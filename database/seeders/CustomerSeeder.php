<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            [
                'name' => 'ملا صاحب',
                'phone' => '0700111222',
                'credit_limit' => 5000.00,
            ],
            [
                'name' => 'اکا نادر',
                'phone' => '0700333444',
                'credit_limit' => 10000.00,
            ],
            [
                'name' => 'کریم جان',
                'phone' => '0788555666',
                'credit_limit' => 3000.00,
            ],
            [
                'name' => 'بي بي فاطمه',
                'phone' => '0799777888',
                'credit_limit' => 2000.00,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::query()->create($customer);
        }
    }
}
