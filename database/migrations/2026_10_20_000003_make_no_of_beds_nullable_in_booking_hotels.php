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
            if (Schema::hasColumn('booking_hotels', 'no_of_beds')) {
                $table->integer('no_of_beds')->nullable()->default(0)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_hotels', function (Blueprint $table) {
            if (Schema::hasColumn('booking_hotels', 'no_of_beds')) {
                $table->integer('no_of_beds')->default(0)->change();
            }
        });
    }
};
