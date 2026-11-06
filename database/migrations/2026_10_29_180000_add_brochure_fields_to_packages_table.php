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
            // A. Price on Request toggle
            if (!Schema::hasColumn('packages', 'is_price_on_request')) {
                $table->boolean('is_price_on_request')->default(false)->after('status');
            }

            // B. Package Excludes Repeater (JSON array of exclusions)
            if (!Schema::hasColumn('packages', 'package_excludes_list')) {
                $table->json('package_excludes_list')->nullable()->after('inclusions');
            }

            // C. Day-by-Day Suggested Itinerary Repeater (JSON column)
            if (!Schema::hasColumn('packages', 'itinerary_days')) {
                $table->json('itinerary_days')->nullable()->after('package_excludes_list');
            }

            // D. Duration Nights Specification
            if (!Schema::hasColumn('packages', 'duration_nights')) {
                $table->integer('duration_nights')->nullable()->after('stay_nights');
            }

            // E. Sightseeing & Excursion Summary
            if (!Schema::hasColumn('packages', 'sightseeing_summary')) {
                $table->string('sightseeing_summary', 255)->nullable()->after('subtitle');
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
                'is_price_on_request',
                'package_excludes_list',
                'itinerary_days',
                'duration_nights',
                'sightseeing_summary',
            ];

            $dropCols = array_filter($cols, fn($c) => Schema::hasColumn('packages', $c));
            if (!empty($dropCols)) {
                $table->dropColumn($dropCols);
            }
        });
    }
};
