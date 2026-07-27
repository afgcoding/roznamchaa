<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $oils = Category::query()->where('name', 'روغنیات')->first();
        $grains = Category::query()->where('name', 'حبوبات')->first();
        $beverages = Category::query()->where('name', 'مشروبات')->first();
        $biscuits = Category::query()->where('name', 'بسکټ')->first();

        $carton = Unit::query()->where('short_name', 'ctn')->first();
        $bag = Unit::query()->where('short_name', 'bag')->first();
        $piece = Unit::query()->where('short_name', 'pcs')->first();
        $kg = Unit::query()->where('short_name', 'kg')->first();

        $products = [
            [
                'category_id' => $oils->id,
                'name' => 'د پخلی غوړي ۱.۸ لیټره',
                'barcode' => '8901001001001',
                'purchase_unit_id' => $carton->id,
                'sale_unit_id' => $piece->id,
                'unit_conversion' => 12,
                'cost_price' => 180.00,
                'sale_price' => 210.00,
                'stock_quantity' => 48,
                'min_stock_alert' => 10,
            ],
            [
                'category_id' => $grains->id,
                'name' => 'سيلا وریجې',
                'barcode' => '8901001001002',
                'purchase_unit_id' => $bag->id,
                'sale_unit_id' => $kg->id,
                'unit_conversion' => 50,
                'cost_price' => 70.00,
                'sale_price' => 85.00,
                'stock_quantity' => 120,
                'min_stock_alert' => 20,
            ],
            [
                'category_id' => $beverages->id,
                'name' => 'پیپسي ۱.۵ لیټره',
                'barcode' => '8901001001003',
                'purchase_unit_id' => $carton->id,
                'sale_unit_id' => $piece->id,
                'unit_conversion' => 12,
                'cost_price' => 40.00,
                'sale_price' => 50.00,
                'stock_quantity' => 60,
                'min_stock_alert' => 12,
            ],
            [
                'category_id' => $beverages->id,
                'name' => 'منرال اوبه ۰.۵ لیټره',
                'barcode' => null,
                'purchase_unit_id' => $carton->id,
                'sale_unit_id' => $piece->id,
                'unit_conversion' => 24,
                'cost_price' => 8.00,
                'sale_price' => 12.00,
                'stock_quantity' => 96,
                'min_stock_alert' => 24,
            ],
            [
                'category_id' => $biscuits->id,
                'name' => 'سوپر بسکټ',
                'barcode' => '8901001001005',
                'purchase_unit_id' => $carton->id,
                'sale_unit_id' => $piece->id,
                'unit_conversion' => 24,
                'cost_price' => 25.00,
                'sale_price' => 35.00,
                'stock_quantity' => 72,
                'min_stock_alert' => 15,
            ],
            [
                'category_id' => $grains->id,
                'name' => 'سوره لوبيا',
                'barcode' => null,
                'purchase_unit_id' => $bag->id,
                'sale_unit_id' => $kg->id,
                'unit_conversion' => 25,
                'cost_price' => 90.00,
                'sale_price' => 110.00,
                'stock_quantity' => 40,
                'min_stock_alert' => 5,
            ],
        ];

        foreach ($products as $product) {
            Product::query()->create($product);
        }
    }
}
