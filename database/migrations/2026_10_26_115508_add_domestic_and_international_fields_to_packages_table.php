<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * NOTE: Inspect and run manually as specified in guidelines.
     */
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Location & Route Context
            if (!Schema::hasColumn('packages', 'departure_city')) {
                $table->string('departure_city')->nullable()->after('subtitle');
            }
            if (!Schema::hasColumn('packages', 'destination_city')) {
                $table->string('destination_city')->nullable()->after('departure_city');
            }
            if (!Schema::hasColumn('packages', 'destination_country')) {
                $table->string('destination_country')->nullable()->after('destination_city');
            }
            if (!Schema::hasColumn('packages', 'is_multi_city')) {
                $table->boolean('is_multi_city')->default(false)->after('destination_country');
            }

            // Lodging & Stay
            if (!Schema::hasColumn('packages', 'primary_hotel')) {
                $table->string('primary_hotel')->nullable()->after('is_multi_city');
            }
            if (!Schema::hasColumn('packages', 'hotel_star_rating')) {
                $table->string('hotel_star_rating')->nullable()->after('primary_hotel');
            }
            if (!Schema::hasColumn('packages', 'stay_nights')) {
                $table->integer('stay_nights')->nullable()->after('hotel_star_rating');
            }
            if (!Schema::hasColumn('packages', 'meal_plan')) {
                $table->string('meal_plan')->nullable()->after('stay_nights');
            }

            // Flights & Ground Logistics
            if (!Schema::hasColumn('packages', 'ground_transfer_fleet')) {
                $table->string('ground_transfer_fleet')->nullable()->after('meal_plan');
            }

            // Pricing Matrix: Per-person rates
            if (!Schema::hasColumn('packages', 'price_per_person')) {
                $table->decimal('price_per_person', 12, 2)->default(0)->after('price_double');
            }

            // Dynamic Content & Rich Text: Hotel Amenities, Inclusions list, Description notes, Banner image
            if (!Schema::hasColumn('packages', 'hotel_amenities')) {
                $table->json('hotel_amenities')->nullable()->after('inclusions');
            }
            if (!Schema::hasColumn('packages', 'description_notes')) {
                $table->longText('description_notes')->nullable()->after('requirements_text');
            }
            if (!Schema::hasColumn('packages', 'banner_image')) {
                $table->string('banner_image', 255)->nullable()->after('package_image');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $cols = [
                'departure_city',
                'destination_city',
                'destination_country',
                'is_multi_city',
                'primary_hotel',
                'hotel_star_rating',
                'stay_nights',
                'meal_plan',
                'ground_transfer_fleet',
                'price_per_person',
                'hotel_amenities',
                'description_notes',
                'banner_image',
            ];

            $dropCols = array_filter($cols, fn($c) => Schema::hasColumn('packages', $c));
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
