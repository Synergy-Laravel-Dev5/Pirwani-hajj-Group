<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('supplier_code', 50)->nullable()->unique();
                $table->string('contact_person')->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('mobile', 50)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('service_type', 100)->nullable()->default('Hotels & Accommodation');
                $table->string('country', 100)->default('Saudi Arabia');
                $table->string('city', 100)->nullable();
                $table->text('address')->nullable();
                $table->string('tax_number', 100)->nullable();
                $table->string('commercial_reg_no', 100)->nullable();
                $table->string('currency', 10)->default('SAR');
                $table->decimal('opening_balance', 14, 2)->default(0.00);
                $table->string('bank_name')->nullable();
                $table->string('bank_account_title')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('bank_iban')->nullable();
                $table->string('status', 20)->default('active'); // active, inactive
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (Schema::hasTable('hotel_sales')) {
            Schema::table('hotel_sales', function (Blueprint $table) {
                if (!Schema::hasColumn('hotel_sales', 'supplier_id')) {
                    $table->foreignId('supplier_id')->nullable()->after('company_id')->constrained('suppliers')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hotel_sales')) {
            Schema::table('hotel_sales', function (Blueprint $table) {
                if (Schema::hasColumn('hotel_sales', 'supplier_id')) {
                    $table->dropForeign(['supplier_id']);
                    $table->dropColumn('supplier_id');
                }
            });
        }

        Schema::dropIfExists('suppliers');
    }
};
