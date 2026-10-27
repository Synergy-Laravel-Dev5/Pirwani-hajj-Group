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
        Schema::table('visa_countries', function (Blueprint $table) {
            if (!Schema::hasColumn('visa_countries', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            if (Schema::hasColumn('visa_countries', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
