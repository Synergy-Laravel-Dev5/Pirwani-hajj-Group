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
        Schema::create('person_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('booking_persons')->cascadeOnDelete();

            // All document paths are optional/nullable
            $table->string('nic_front')->nullable();
            $table->string('nic_back')->nullable();
            $table->string('visa')->nullable();
            $table->string('passport')->nullable();
            $table->string('ticket')->nullable();

            $table->timestamps();

            // Ensure one document record per family member
            $table->unique('person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_documents');
    }
};
