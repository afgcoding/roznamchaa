<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds system-owner role without wiping users.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('super_admin', 'admin', 'cashier') NOT NULL DEFAULT 'cashier'");

        // Promote the original seeded system owner if present.
        DB::table('users')
            ->where('email', 'admin@gmail.com')
            ->update(['role' => 'super_admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')
            ->where('role', 'super_admin')
            ->update(['role' => 'admin']);

        DB::statement("ALTER TABLE `users` MODIFY `role` ENUM('admin', 'cashier') NOT NULL DEFAULT 'cashier'");
    }
};
