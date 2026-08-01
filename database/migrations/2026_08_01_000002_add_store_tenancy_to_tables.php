<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that own data per store (store_id foreign key).
     *
     * @var list<string>
     */
    private array $tenantTables = [
        'categories',
        'units',
        'wholesalers',
        'customers',
        'expenses',
        'products',
        'sales',
        'sale_items',
        'customer_ledgers',
        'wholesaler_payments',
    ];

    /**
     * Run the migrations.
     * Adds store membership pivot + store_id FKs without wiping existing data.
     */
    public function up(): void
    {
        Schema::create('store_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['store_id', 'user_id']);
        });

        $now = now();

        $storeId = DB::table('stores')->insertGetId([
            'name' => 'Main Store',
            'slug' => 'main',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $userRows = DB::table('users')->pluck('id')->map(fn ($userId): array => [
            'store_id' => $storeId,
            'user_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        if ($userRows !== []) {
            DB::table('store_user')->insert($userRows);
        }

        foreach ($this->tenantTables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->unsignedBigInteger('store_id')->nullable()->after('id');
            });

            DB::table($tableName)->whereNull('store_id')->update(['store_id' => $storeId]);

            DB::statement("ALTER TABLE `{$tableName}` MODIFY `store_id` BIGINT UNSIGNED NOT NULL");

            Schema::table($tableName, function (Blueprint $table) {
                $table->foreign('store_id')
                    ->references('id')
                    ->on('stores')
                    ->cascadeOnDelete();
            });
        }

        // Barcodes should be unique per store, not globally.
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['barcode']);
            $table->unique(['store_id', 'barcode']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropUnique(['store_id', 'barcode']);
            $table->unique('barcode');
        });

        foreach (array_reverse($this->tenantTables) as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropForeign(['store_id']);
                $table->dropColumn('store_id');
            });
        }

        Schema::dropIfExists('store_user');

        DB::table('stores')->where('slug', 'main')->delete();
    }
};
