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
            if (!Schema::hasColumn('booking_persons', 'gender')) {
                $table->string('gender', 20)->nullable()->default('Male')->after('full_name');
            }
            if (!Schema::hasColumn('booking_persons', 'hajj_id')) {
                $table->string('hajj_id', 50)->nullable()->after('booking_id');
            }
            if (!Schema::hasColumn('booking_persons', 'hb_number')) {
                $table->string('hb_number', 50)->nullable()->after('hajj_id');
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
            if (Schema::hasColumn('booking_persons', 'gender')) $cols[] = 'gender';
            if (Schema::hasColumn('booking_persons', 'hajj_id')) $cols[] = 'hajj_id';
            if (Schema::hasColumn('booking_persons', 'hb_number')) $cols[] = 'hb_number';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
