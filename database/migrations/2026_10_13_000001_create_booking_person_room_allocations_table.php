<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('booking_person_room_allocations')) {
            Schema::dropIfExists('booking_person_room_allocations');
        }

        Schema::create('booking_person_room_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id')->nullable()->index('bpra_b_id_idx');
            $table->unsignedBigInteger('booking_person_id')->nullable()->index('bpra_bp_id_idx');
            $table->string('location', 100)->default('makkah');
            $table->string('hotel_name', 191);
            $table->string('room_number', 100)->nullable();
            $table->string('room_type', 100)->nullable();
            $table->string('gender', 50)->nullable()->default('Any');
            $table->date('check_in')->nullable();
            $table->date('check_out')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['booking_id', 'booking_person_id'], 'bpra_bk_person_idx');
            $table->index(['hotel_name', 'room_number'], 'bpra_htl_rm_idx');
            $table->index(['location', 'hotel_name'], 'bpra_loc_htl_idx');
        });

        // Migrate any existing room allocations from booking_persons for seamless continuity
        if (Schema::hasTable('booking_persons')) {
            $existingPersons = DB::table('booking_persons')
                ->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->whereNotNull('room_number')
                            ->where('room_number', '!=', '')
                            ->where('room_number', '!=', 'PENDING')
                            ->where('room_number', '!=', 'UNASSIGNED');
                    })->orWhere(function ($sub) {
                        $sub->whereNotNull('hotel_name')
                            ->where('hotel_name', '!=', '')
                            ->where('hotel_name', '!=', 'Pending Room Allocation')
                            ->where('hotel_name', '!=', 'Unspecified Hotel');
                    });
                })
                ->get();

            foreach ($existingPersons as $p) {
                $hName = trim($p->hotel_name ?? '');
                $rNum  = trim($p->room_number ?? '');
                if (!empty($hName) || !empty($rNum)) {
                    DB::table('booking_person_room_allocations')->insert([
                        'booking_id'        => $p->booking_id,
                        'booking_person_id' => $p->id,
                        'location'          => $p->location ?: 'makkah',
                        'hotel_name'        => !empty($hName) ? $hName : 'Unspecified Hotel',
                        'room_number'       => !empty($rNum) ? $rNum : null,
                        'room_type'         => $p->room_type ?: 'Quad',
                        'gender'            => $p->room_gender ?: 'Any',
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_person_room_allocations');
    }
};
