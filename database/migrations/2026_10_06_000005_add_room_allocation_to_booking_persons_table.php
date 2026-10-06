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
        Schema::table('booking_persons', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_persons', 'room_number')) {
                $table->string('room_number', 100)->nullable()->after('photo');
            }
            if (!Schema::hasColumn('booking_persons', 'room_type')) {
                $table->string('room_type', 100)->nullable()->after('room_number');
            }
            if (!Schema::hasColumn('booking_persons', 'hotel_name')) {
                $table->string('hotel_name', 191)->nullable()->after('room_type');
            }
            if (!Schema::hasColumn('booking_persons', 'location')) {
                $table->string('location', 100)->nullable()->default('makkah')->after('hotel_name');
            }
            if (!Schema::hasColumn('booking_persons', 'room_gender')) {
                $table->string('room_gender', 50)->nullable()->default('Any')->after('location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_persons', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('booking_persons', 'room_number')) $cols[] = 'room_number';
            if (Schema::hasColumn('booking_persons', 'room_type')) $cols[] = 'room_type';
            if (Schema::hasColumn('booking_persons', 'hotel_name')) $cols[] = 'hotel_name';
            if (Schema::hasColumn('booking_persons', 'location')) $cols[] = 'location';
            if (Schema::hasColumn('booking_persons', 'room_gender')) $cols[] = 'room_gender';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
