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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'app_password')) {
                $table->string('app_password')->nullable()->after('balance');
            }
            if (!Schema::hasColumn('bookings', 'app_password_plain')) {
                $table->string('app_password_plain', 20)->nullable()->after('app_password');
            }
            if (!Schema::hasColumn('bookings', 'app_last_login_at')) {
                $table->timestamp('app_last_login_at')->nullable()->after('app_password_plain');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['app_password', 'app_password_plain', 'app_last_login_at']);
        });
    }
};
