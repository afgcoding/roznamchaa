<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * د اجناسو اصلي جدول (محصولات او سټاک)
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->string('name');
            $table->string('barcode')->nullable()->unique();
            $table->foreignId('purchase_unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('sale_unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('unit_conversion', 12, 3)->default(1);
            $table->decimal('cost_price', 12, 2);
            $table->decimal('sale_price', 12, 2);
            $table->decimal('stock_quantity', 12, 3)->default(0);
            $table->unsignedInteger('min_stock_alert')->default(5);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
