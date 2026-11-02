<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('booking_person_room_allocations') && !Schema::hasColumn('booking_person_room_allocations', 'room_group_id')) {
            Schema::table('booking_person_room_allocations', function (Blueprint $table) {
                $table->string('room_group_id', 100)->nullable()->after('room_number')->index();
            });
        }
        if (Schema::hasTable('booking_persons') && !Schema::hasColumn('booking_persons', 'room_group_id')) {
            Schema::table('booking_persons', function (Blueprint $table) {
                $table->string('room_group_id', 100)->nullable()->after('room_number')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('booking_person_room_allocations') && Schema::hasColumn('booking_person_room_allocations', 'room_group_id')) {
            Schema::table('booking_person_room_allocations', function (Blueprint $table) {
                $table->dropColumn('room_group_id');
            });
        }
        if (Schema::hasTable('booking_persons') && Schema::hasColumn('booking_persons', 'room_group_id')) {
            Schema::table('booking_persons', function (Blueprint $table) {
                $table->dropColumn('room_group_id');
            });
        }
    }
};
