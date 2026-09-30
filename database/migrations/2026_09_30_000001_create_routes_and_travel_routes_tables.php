<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('routes')) {
            Schema::create('routes', function (Blueprint $table) {
                $table->id();
                $table->string('start_place');
                $table->string('end_place');
                $table->string('status')->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('travel_routes')) {
            Schema::create('travel_routes', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->date('arrival_date')->nullable();
                $table->time('arrival_time')->nullable();
                $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
                $table->string('sharing_type')->nullable(); // Group, Private, Economy
                $table->string('status')->default('active');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('travel_route_routes')) {
            Schema::create('travel_route_routes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('travel_route_id')->constrained('travel_routes')->cascadeOnDelete();
                $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('travel_route_routes');
        Schema::dropIfExists('travel_routes');
        Schema::dropIfExists('routes');
    }
};
