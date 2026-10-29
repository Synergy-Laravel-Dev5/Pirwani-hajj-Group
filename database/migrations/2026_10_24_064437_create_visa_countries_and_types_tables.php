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
        Schema::create('visa_countries', function (Blueprint $table) {
            $table->id();
            $table->string('country_name', 150);
            $table->string('country_code', 10);
            $table->string('monument_picture')->nullable();
            $table->string('country_flag')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visa_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_country_id')->constrained('visa_countries')->onDelete('cascade');
            $table->string('visa_name', 150);
            $table->decimal('b2b_rate_pkr', 12, 2)->default(0);
            $table->decimal('visa_fee_usd', 10, 2)->default(0);
            $table->string('process_time', 100)->nullable();
            $table->text('notes')->nullable();
            $table->json('requirements')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_types');
        Schema::dropIfExists('visa_countries');
    }
};
