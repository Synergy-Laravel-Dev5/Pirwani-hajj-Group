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
            if (!Schema::hasColumn('packages', 'subtitle')) {
                $table->string('subtitle')->nullable()->after('package_title');
            }
            if (!Schema::hasColumn('packages', 'additional_destinations')) {
                $table->json('additional_destinations')->nullable()->after('is_dual_country');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('packages', 'subtitle')) {
                $cols[] = 'subtitle';
            }
            if (Schema::hasColumn('packages', 'additional_destinations')) {
                $cols[] = 'additional_destinations';
            }
            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
