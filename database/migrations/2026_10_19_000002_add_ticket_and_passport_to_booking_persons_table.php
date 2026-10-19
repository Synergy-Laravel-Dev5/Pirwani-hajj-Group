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
            if (!Schema::hasColumn('booking_persons', 'ticket_status')) {
                $table->string('ticket_status', 50)->default('on_ticket')->nullable()->after('photo');
            }
            if (!Schema::hasColumn('booking_persons', 'passport_image')) {
                $table->string('passport_image', 255)->nullable()->after('ticket_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_persons', function (Blueprint $table) {
            if (Schema::hasColumn('booking_persons', 'ticket_status')) {
                $table->dropColumn('ticket_status');
            }
            if (Schema::hasColumn('booking_persons', 'passport_image')) {
                $table->dropColumn('passport_image');
            }
        });
    }
};
