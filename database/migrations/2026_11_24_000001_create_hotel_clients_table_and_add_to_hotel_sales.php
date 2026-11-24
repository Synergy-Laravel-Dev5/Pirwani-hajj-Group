<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hotel_clients')) {
            Schema::create('hotel_clients', function (Blueprint $table) {
                $table->id();
                $table->string('company_name')->nullable();
                $table->string('name');
                $table->string('contact', 100)->nullable();
                $table->string('status', 20)->default('active'); // active, inactive
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('hotel_sales')) {
            Schema::table('hotel_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('hotel_sales', 'hotel_client_id')) {
                    $table->foreignId('hotel_client_id')->nullable()->after('client_id')->constrained('hotel_clients')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hotel_sales')) {
            Schema::table('hotel_sales', function (Blueprint $table) {
                if (Schema::hasColumn('hotel_sales', 'hotel_client_id')) {
                    $table->dropForeign(['hotel_client_id']);
                    $table->dropColumn('hotel_client_id');
                }
            });
        }

        Schema::dropIfExists('hotel_clients');
    }
};
