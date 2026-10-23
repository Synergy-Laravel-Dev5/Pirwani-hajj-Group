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
            $table->string('app_password')->nullable()->after('balance');
            $table->string('app_password_plain', 20)->nullable()->after('app_password');
            $table->timestamp('app_last_login_at')->nullable()->after('app_password_plain');
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
