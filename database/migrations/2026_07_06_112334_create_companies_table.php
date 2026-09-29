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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            // Company Details
            $table->string('company_name')->nullable();
            $table->string('company_code')->unique()->nullable();
            $table->string('currency_type')->nullable();
            $table->date('established_on')->nullable();
            $table->integer('quota')->nullable();
            $table->string('website')->nullable();
            $table->string('company_status_on_establishment')->nullable();
            $table->string('current_company_status')->nullable();

            // Files
            $table->string('company_logo')->nullable();
            $table->string('company_stamp')->nullable();
            $table->string('letter_head_header')->nullable();
            $table->string('letter_head_footer')->nullable();
            $table->string('company_signature')->nullable();

            // Company Registrations
            $table->string('mohra_enrollment_no')->nullable();
            $table->string('munazzam_no')->nullable();
            $table->string('cluster_enrollment_no')->nullable();
            $table->string('dts_no')->nullable();
            $table->date('dts_expiry')->nullable();
            $table->string('iata_no')->nullable();
            $table->date('iata_expiry')->nullable();
            $table->string('ntn')->nullable();

            // Director Details
            $table->string('director_name')->nullable();
            $table->string('director_cnic')->nullable();
            $table->date('director_cnic_expiry')->nullable();
            $table->string('director_photo')->nullable();
            $table->text('director_detail')->nullable();

            // Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_title')->nullable();
            $table->string('branch')->nullable();
            $table->string('account_number')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
