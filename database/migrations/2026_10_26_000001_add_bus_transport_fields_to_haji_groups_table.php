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
        Schema::table('haji_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('haji_groups', 'route_name')) {
                $table->string('route_name')->nullable()->after('bus_capacity');
            }
            if (!Schema::hasColumn('haji_groups', 'driver_name')) {
                $table->string('driver_name')->nullable()->after('route_name');
            }
            if (!Schema::hasColumn('haji_groups', 'driver_phone')) {
                $table->string('driver_phone')->nullable()->after('driver_name');
            }
            if (!Schema::hasColumn('haji_groups', 'plate_number')) {
                $table->string('plate_number')->nullable()->after('driver_phone');
            }
            if (!Schema::hasColumn('haji_groups', 'departure_date')) {
                $table->date('departure_date')->nullable()->after('plate_number');
            }
            if (!Schema::hasColumn('haji_groups', 'departure_time')) {
                $table->time('departure_time')->nullable()->after('departure_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('haji_groups', function (Blueprint $table) {
            $cols = ['route_name', 'driver_name', 'driver_phone', 'plate_number', 'departure_date', 'departure_time'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('haji_groups', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
