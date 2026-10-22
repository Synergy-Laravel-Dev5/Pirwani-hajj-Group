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
        Schema::create('package_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->cascadeOnDelete();

            // Applicant Details
            $table->string('full_name');
            $table->string('contact');
            $table->string('email');
            $table->string('passport_number')->nullable();
            $table->string('cnic_number')->nullable();
            $table->unsignedInteger('pax')->default(1);
            $table->enum('room_type', ['sharing', 'quad', 'triple', 'double']);
            $table->text('special_request')->nullable();

            // Attached Documents (stored file paths)
            $table->string('passport_document')->nullable();
            $table->string('nic_or_visa_document')->nullable();

            // Operational status for Admin Panel
            $table->enum('status', ['pending', 'contacted', 'closed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_inquiries');
    }
};
