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
        // Modify location and room_type in booking_hotels to VARCHAR so any location (azizia, mina, etc.) and room_type (sharing, quint, suite, etc.) can be saved without truncation errors
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->string('location', 100)->nullable()->default('makkah')->change();
            $table->string('room_type', 100)->nullable()->default('quad')->change();
        });

        // Also ensure booking_transports transport_type is flexible VARCHAR
        if (Schema::hasTable('booking_transports')) {
            Schema::table('booking_transports', function (Blueprint $table) {
                $table->string('transport_type', 100)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->enum('location', ['makkah', 'madinah', 'other'])->default('makkah')->change();
            $table->enum('room_type', ['single', 'double', 'triple', 'quad', 'suite'])->change();
        });
    }
};
