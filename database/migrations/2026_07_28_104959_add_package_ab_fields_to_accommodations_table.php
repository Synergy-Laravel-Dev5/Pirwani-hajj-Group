<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('package_accommodations', function (Blueprint $table) {
            // Package A
            $table->string('package_a_accommodation_type', 150)->nullable()->after('hotel');
            $table->string('package_a_saudi_star_rating', 50)->nullable()->after('package_a_accommodation_type');
            $table->string('package_a_hotel', 150)->nullable()->after('package_a_saudi_star_rating');

            // Package B
            $table->string('package_b_accommodation_type', 150)->nullable()->after('package_a_hotel');
            $table->string('package_b_saudi_star_rating', 50)->nullable()->after('package_b_accommodation_type');
            $table->string('package_b_hotel', 150)->nullable()->after('package_b_saudi_star_rating');
        });
    }

    public function down(): void
    {
        Schema::table('package_accommodations', function (Blueprint $table) {
            $table->dropColumn([
                'package_a_accommodation_type',
                'package_a_saudi_star_rating',
                'package_a_hotel',
                'package_b_accommodation_type',
                'package_b_saudi_star_rating',
                'package_b_hotel',
            ]);
        });
    }
};
