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
        Schema::create('visa_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visa_country_id')->constrained('visa_countries')->onDelete('cascade');
            $table->foreignId('visa_type_id')->nullable()->constrained('visa_types')->onDelete('set null');

            // Personal Information
            $table->string('full_name', 150);
            $table->string('phone_number', 30);
            $table->string('email', 150)->nullable();
            $table->string('cnic', 30);

            // Dynamic Documents Array (Replaces all individual file columns)
            $table->json('documents')->nullable();

            $table->enum('status', ['pending', 'contacted', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_inquiries');
    }
};
