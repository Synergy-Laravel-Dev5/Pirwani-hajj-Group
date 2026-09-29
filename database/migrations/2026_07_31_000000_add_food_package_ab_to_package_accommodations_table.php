<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_accommodations', function (Blueprint $table) {
            if (!Schema::hasColumn('package_accommodations', 'package_a_food_package')) {
                $table->string('package_a_food_package', 100)->nullable()->after('package_a_hotel');
            }
            if (!Schema::hasColumn('package_accommodations', 'package_b_food_package')) {
                $table->string('package_b_food_package', 100)->nullable()->after('package_b_hotel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('package_accommodations', function (Blueprint $table) {
            $table->dropColumn(['package_a_food_package', 'package_b_food_package']);
        });
    }
};
