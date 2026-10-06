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
        if (!Schema::hasTable('hotel_room_capacities')) {
            Schema::create('hotel_room_capacities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
                $table->string('hotel_name', 191);
                $table->string('location', 100)->nullable();
                $table->string('room_number', 100);
                $table->string('room_type', 100)->nullable();
                $table->integer('bed_capacity')->default(2);
                $table->integer('extra_beds')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['hotel_name', 'room_number']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_room_capacities');
    }
};
