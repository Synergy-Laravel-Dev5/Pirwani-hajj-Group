<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('package_inquiries', function (Blueprint $table) {
            if (!Schema::hasColumn('package_inquiries', 'duration_days')) {
                $table->string('duration_days', 50)->nullable()->after('pax');
            }
        });

        // Make email nullable if it was required before
        try {
            DB::statement("ALTER TABLE `package_inquiries` MODIFY `email` VARCHAR(150) NULL");
        } catch (\Throwable $e) {
            // Ignore if already nullable or driver not supporting
        }

        // Alter status enum to include all requested statuses
        try {
            DB::statement("ALTER TABLE `package_inquiries` MODIFY `status` ENUM('pending', 'contacted', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // Fallback for non-MySQL or error
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('package_inquiries', function (Blueprint $table) {
            if (Schema::hasColumn('package_inquiries', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
        });

        try {
            DB::statement("ALTER TABLE `package_inquiries` MODIFY `status` ENUM('pending', 'contacted', 'closed') NOT NULL DEFAULT 'pending'");
        } catch (\Throwable $e) {
        }
    }
};
