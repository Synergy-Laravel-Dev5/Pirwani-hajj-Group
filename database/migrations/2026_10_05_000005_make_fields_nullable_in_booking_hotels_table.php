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
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->string('location', 100)->nullable()->default('makkah')->change();
            $table->string('room_type', 100)->nullable()->default('quad')->change();
            $table->integer('no_of_rooms')->nullable()->default(1)->change();
            $table->integer('no_of_nights')->nullable()->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->integer('no_of_rooms')->default(1)->change();
            $table->integer('no_of_nights')->default(1)->change();
        });
    }
};
