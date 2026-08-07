<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add subscription plan to stores without touching existing rows.
     * Existing stores receive default "grocery" via column default.
     */
    public function up(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->enum('plan_type', ['grocery', 'wholesale', 'supermarket'])
                ->default('grocery')
                ->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn('plan_type');
        });
    }
};
