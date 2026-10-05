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
            if (!Schema::hasColumn('booking_persons', 'date_of_issue')) {
                $table->date('date_of_issue')->nullable()->after('passport_number');
            }
            if (!Schema::hasColumn('booking_persons', 'passport_issue_date')) {
                $table->date('passport_issue_date')->nullable()->after('date_of_issue');
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
            if (Schema::hasColumn('booking_persons', 'date_of_issue')) $cols[] = 'date_of_issue';
            if (Schema::hasColumn('booking_persons', 'passport_issue_date')) $cols[] = 'passport_issue_date';
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
