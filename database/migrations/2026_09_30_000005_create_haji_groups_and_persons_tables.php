<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('haji_groups')) {
            Schema::create('haji_groups', function (Blueprint $table) {
                $table->id();
                $table->string('group_name');
                $table->string('group_type')->default('general'); // flight, bus, hotel, route, general
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->string('leader_name')->nullable();
                $table->string('leader_phone')->nullable();

                // Flight allocation
                $table->unsignedBigInteger('airline_id')->nullable();
                $table->unsignedBigInteger('flight_id')->nullable();
                $table->string('flight_number')->nullable();
                $table->string('pnr')->nullable();
                $table->date('flight_date')->nullable();

                // Bus / Vehicle allocation
                $table->unsignedBigInteger('vehicle_id')->nullable();
                $table->string('bus_name')->nullable();
                $table->integer('bus_capacity')->default(45);

                // Hotel Accommodation allocation
                $table->unsignedBigInteger('hotel_id')->nullable();
                $table->string('room_type')->nullable(); // Double, Triple, Quad, Sharing etc.

                // Travel & Route
                $table->unsignedBigInteger('route_id')->nullable();
                $table->unsignedBigInteger('travel_route_id')->nullable();

                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('haji_group_persons')) {
            Schema::create('haji_group_persons', function (Blueprint $table) {
                $table->id();
                $table->foreignId('haji_group_id')->constrained('haji_groups')->cascadeOnDelete();
                $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
                $table->foreignId('booking_person_id')->constrained('booking_persons')->cascadeOnDelete();
                $table->string('seat_number')->nullable();
                $table->string('room_number')->nullable();
                $table->string('bed_number')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('haji_group_persons');
        Schema::dropIfExists('haji_groups');
    }
};
