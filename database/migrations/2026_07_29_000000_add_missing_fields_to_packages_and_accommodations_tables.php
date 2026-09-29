<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'hijri_start_day')) {
                $table->integer('hijri_start_day')->nullable()->after('hajj_duration');
            }
            if (!Schema::hasColumn('packages', 'hijri_start_month')) {
                $table->integer('hijri_start_month')->nullable()->after('hijri_start_day');
            }
        });

        Schema::table('package_accommodations', function (Blueprint $table) {
            if (!Schema::hasColumn('package_accommodations', 'same_for_both')) {
                $table->boolean('same_for_both')->default(false)->after('check_out');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['hijri_start_day', 'hijri_start_month']);
        });

        Schema::table('package_accommodations', function (Blueprint $table) {
            $table->dropColumn(['same_for_both']);
        });
    }
};
