<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('hotel_room_inventories')) {
            Schema::create('hotel_room_inventories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->constrained('hotels')->cascadeOnDelete();
                $table->string('batch_name')->nullable();
                $table->string('room_type'); // Double, Triple, Quad, Quint, Single, Sharing, Suite
                $table->string('room_view')->nullable()->default('City View');
                $table->string('meal_plan')->nullable()->default('Room Only');
                $table->date('check_in')->nullable();
                $table->date('check_out')->nullable();
                $table->integer('total_rooms')->default(0);
                $table->integer('male_beds')->default(0);
                $table->integer('female_beds')->default(0);
                $table->integer('total_beds')->default(0);
                $table->decimal('cost_rate', 12, 2)->default(0.00);
                $table->decimal('selling_rate', 12, 2)->default(0.00);
                $table->string('currency')->default('SAR');
                $table->unsignedBigInteger('supplier_id')->nullable();
                $table->string('status')->default('active');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_room_inventories');
    }
};
