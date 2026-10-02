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
            if (!Schema::hasColumn('booking_persons', 'surname')) {
                $table->string('surname', 150)->nullable()->after('full_name');
            }
            if (!Schema::hasColumn('booking_persons', 'given_name')) {
                $table->string('given_name', 150)->nullable()->after('surname');
            }
            if (!Schema::hasColumn('booking_persons', 'dob')) {
                $table->date('dob')->nullable()->after('given_name');
            }
            if (!Schema::hasColumn('booking_persons', 'passport_expiry_date')) {
                $table->date('passport_expiry_date')->nullable()->after('passport_number');
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
            if (Schema::hasColumn('booking_persons', 'surname')) $cols[] = 'surname';
            if (Schema::hasColumn('booking_persons', 'given_name')) $cols[] = 'given_name';
            if (Schema::hasColumn('booking_persons', 'dob')) $cols[] = 'dob';
            if (Schema::hasColumn('booking_persons', 'passport_expiry_date')) $cols[] = 'passport_expiry_date';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
