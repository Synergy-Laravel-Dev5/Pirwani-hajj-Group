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
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'services_title')) {
                $table->string('services_title', 255)->nullable()->after('price_disclaimer');
            }
            if (!Schema::hasColumn('packages', 'services_content')) {
                $table->longText('services_content')->nullable()->after('services_title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['services_title', 'services_content']);
        });
    }
};
