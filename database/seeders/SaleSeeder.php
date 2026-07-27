<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\CustomerLedger;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Also creates sale_items and related customer_ledgers for credit/partial sales.
     */
    public function run(): void
    {
        $cashier = User::query()->where('email', 'cashier@gmail.com')->first();
        $admin = User::query()->where('email', 'admin@gmail.com')->first();

        $customer1 = Customer::query()->where('name', 'ملا صاحب')->first();
        $customer2 = Customer::query()->where('name', 'اکا نادر')->first();

        $pepsi = Product::query()->where('name', 'پیپسي ۱.۵ لیټره')->first();
        $rice = Product::query()->where('name', 'سيلا وریجې')->first();
        $oil = Product::query()->where('name', 'د پخلی غوړي ۱.۸ لیټره')->first();
        $biscuit = Product::query()->where('name', 'سوپر بسکټ')->first();

        // ۱) نقد خرڅلاو (پرته له مشتري)
        $cashSale = Sale::query()->create([
            'user_id' => $cashier->id,
            'customer_id' => null,
            'total_amount' => 150.00,
            'discount' => 0,
            'payable_amount' => 150.00,
            'paid_amount' => 150.00,
            'due_amount' => 0,
            'payment_status' => 'cash',
        ]);

        SaleItem::query()->create([
            'sale_id' => $cashSale->id,
            'product_id' => $pepsi->id,
            'quantity' => 2,
            'unit_price' => 50.00,
            'cost_price' => 40.00,
            'subtotal' => 100.00,
        ]);

        SaleItem::query()->create([
            'sale_id' => $cashSale->id,
            'product_id' => $biscuit->id,
            'quantity' => 1,
            'unit_price' => 50.00,
            'cost_price' => 25.00,
            'subtotal' => 50.00,
        ]);

        // ۲) نسیه / پور خرڅلاو
        $creditSale = Sale::query()->create([
            'user_id' => $cashier->id,
            'customer_id' => $customer1->id,
            'total_amount' => 420.00,
            'discount' => 20.00,
            'payable_amount' => 400.00,
            'paid_amount' => 0,
            'due_amount' => 400.00,
            'payment_status' => 'credit',
        ]);

        SaleItem::query()->create([
            'sale_id' => $creditSale->id,
            'product_id' => $oil->id,
            'quantity' => 2,
            'unit_price' => 210.00,
            'cost_price' => 180.00,
            'subtotal' => 420.00,
        ]);

        CustomerLedger::query()->create([
            'customer_id' => $customer1->id,
            'sale_id' => $creditSale->id,
            'type' => 'credit',
            'amount' => 400.00,
            'date' => now()->toDateString(),
            'description' => 'د بل نمبر '.$creditSale->id.' پور',
        ]);

        // ۳) جزوي تادیه
        $partialSale = Sale::query()->create([
            'user_id' => $admin->id,
            'customer_id' => $customer2->id,
            'total_amount' => 850.00,
            'discount' => 0,
            'payable_amount' => 850.00,
            'paid_amount' => 500.00,
            'due_amount' => 350.00,
            'payment_status' => 'partial',
        ]);

        SaleItem::query()->create([
            'sale_id' => $partialSale->id,
            'product_id' => $rice->id,
            'quantity' => 10,
            'unit_price' => 85.00,
            'cost_price' => 70.00,
            'subtotal' => 850.00,
        ]);

        CustomerLedger::query()->create([
            'customer_id' => $customer2->id,
            'sale_id' => $partialSale->id,
            'type' => 'credit',
            'amount' => 350.00,
            'date' => now()->toDateString(),
            'description' => 'د بل نمبر '.$partialSale->id.' پاتې پور',
        ]);

        // جلا د پور تادیه (پرته له بل)
        CustomerLedger::query()->create([
            'customer_id' => $customer1->id,
            'sale_id' => null,
            'type' => 'payment',
            'amount' => 100.00,
            'date' => now()->toDateString(),
            'description' => 'د پخواني پور نقد تادیه',
        ]);
    }
}
