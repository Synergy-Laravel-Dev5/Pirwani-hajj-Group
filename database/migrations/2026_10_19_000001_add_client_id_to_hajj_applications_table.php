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
        if (Schema::hasTable('hajj_applications')) {
            Schema::table('hajj_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('hajj_applications', 'client_id')) {
                    $table->unsignedBigInteger('client_id')->nullable()->after('package_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('hajj_applications') && Schema::hasColumn('hajj_applications', 'client_id')) {
            Schema::table('hajj_applications', function (Blueprint $table) {
                $table->dropColumn('client_id');
            });
        }
    }
};
