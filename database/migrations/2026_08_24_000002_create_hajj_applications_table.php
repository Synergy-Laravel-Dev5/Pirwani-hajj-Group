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
        Schema::create('hajj_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('year', 50)->nullable()->default('2027 / 1448 AH');
            $table->string('package_name', 255)->nullable();
            $table->string('photo', 255)->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('surname', 150)->nullable();
            $table->string('given_name', 150)->nullable();
            $table->string('cnic_no', 50)->nullable();
            $table->date('dob')->nullable();
            $table->string('passport_no', 50)->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('father_or_husband_name', 150)->nullable();
            $table->text('postal_address')->nullable();
            $table->string('tehsil_code', 50)->nullable();
            $table->string('mobile_no', 50)->nullable();
            $table->string('telephone_no', 50)->nullable();
            $table->string('is_married', 20)->nullable();
            $table->string('fiqah', 100)->nullable();
            $table->string('blood_group', 20)->nullable();
            $table->string('performed_hajj_last_5_years', 10)->nullable();
            $table->string('hajj_e_badal', 10)->nullable();
            $table->string('group_worker', 10)->nullable();
            $table->string('is_mehram_of_lady', 10)->nullable();

            // Emergency Nominee
            $table->string('nominee_name', 150)->nullable();
            $table->string('nominee_contact', 50)->nullable();
            $table->string('nominee_cnic', 50)->nullable();
            $table->string('nominee_relation', 100)->nullable();

            // Mehram Details
            $table->string('mehram_name', 150)->nullable();
            $table->string('mehram_relation', 100)->nullable();

            $table->text('signature')->nullable();
            $table->string('status', 50)->default('pending');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hajj_applications');
    }
};
