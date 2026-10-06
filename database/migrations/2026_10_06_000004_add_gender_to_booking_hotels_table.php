<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('booking_hotels') && !Schema::hasColumn('booking_hotels', 'gender')) {
            Schema::table('booking_hotels', function (Blueprint $table) {
                $table->string('gender', 50)->nullable()->default('Any')->after('room_number');
            });
        }

        if (Schema::hasTable('hotel_room_capacities') && !Schema::hasColumn('hotel_room_capacities', 'gender')) {
            Schema::table('hotel_room_capacities', function (Blueprint $table) {
                $table->string('gender', 50)->nullable()->default('Any')->after('room_type');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('booking_hotels') && Schema::hasColumn('booking_hotels', 'gender')) {
            Schema::table('booking_hotels', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }

        if (Schema::hasTable('hotel_room_capacities') && Schema::hasColumn('hotel_room_capacities', 'gender')) {
            Schema::table('hotel_room_capacities', function (Blueprint $table) {
                $table->dropColumn('gender');
            });
        }
    }
};
