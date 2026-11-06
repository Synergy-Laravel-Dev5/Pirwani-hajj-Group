<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('qurbani_batches')) {
            Schema::create('qurbani_batches', function (Blueprint $table) {
                $table->id();
                $table->string('batch_name');
                $table->date('qurbani_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->string('title')->nullable(); // announcement title
                $table->text('message')->nullable(); // announcement message
                $table->string('status')->default('scheduled'); // scheduled, fired, completed, cancelled
                $table->timestamp('fired_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('qurbani_batch_hajjis')) {
            Schema::create('qurbani_batch_hajjis', function (Blueprint $table) {
                $table->id();
                $table->foreignId('qurbani_batch_id')->constrained('qurbani_batches')->cascadeOnDelete();
                $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
                $table->foreignId('booking_person_id')->constrained('booking_persons')->cascadeOnDelete();
                $table->string('status')->default('assigned'); // assigned, notified, completed
                $table->timestamp('notified_at')->nullable();
                $table->timestamps();

                $table->unique(['qurbani_batch_id', 'booking_person_id'], 'qurbani_batch_person_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('qurbani_batch_hajjis');
        Schema::dropIfExists('qurbani_batches');
    }
};
