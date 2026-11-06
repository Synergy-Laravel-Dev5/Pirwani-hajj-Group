<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('qurbani_batch_hajjis') && !Schema::hasColumn('qurbani_batch_hajjis', 'completed_at')) {
            Schema::table('qurbani_batch_hajjis', function (Blueprint $table) {
                $table->timestamp('completed_at')->nullable()->after('notified_at');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('qurbani_batch_hajjis') && Schema::hasColumn('qurbani_batch_hajjis', 'completed_at')) {
            Schema::table('qurbani_batch_hajjis', function (Blueprint $table) {
                $table->dropColumn('completed_at');
            });
        }
    }
};
