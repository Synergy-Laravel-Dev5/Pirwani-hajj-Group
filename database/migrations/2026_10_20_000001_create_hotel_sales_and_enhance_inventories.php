<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhance hotel_room_inventories (Purchase Stock Allotment)
        Schema::table('hotel_room_inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('hotel_room_inventories', 'allotment_basis')) {
                $table->string('allotment_basis', 20)->default('room')->after('batch_name'); // 'room' or 'bed'
            }
            if (!Schema::hasColumn('hotel_room_inventories', 'hijri_check_in')) {
                $table->string('hijri_check_in', 100)->nullable()->after('check_in');
            }
            if (!Schema::hasColumn('hotel_room_inventories', 'hijri_check_out')) {
                $table->string('hijri_check_out', 100)->nullable()->after('check_out');
            }
            if (!Schema::hasColumn('hotel_room_inventories', 'no_of_nights')) {
                $table->integer('no_of_nights')->default(1)->after('total_beds');
            }
            if (!Schema::hasColumn('hotel_room_inventories', 'total_cost')) {
                $table->decimal('total_cost', 14, 2)->default(0.00)->after('cost_rate');
            }
            if (!Schema::hasColumn('hotel_room_inventories', 'total_selling')) {
                $table->decimal('total_selling', 14, 2)->default(0.00)->after('selling_rate');
            }
        });

        // 2. Enhance booking_hotels (Sale in general bookings)
        Schema::table('booking_hotels', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_hotels', 'allotment_basis')) {
                $table->string('allotment_basis', 20)->default('room')->after('room_type'); // 'room' or 'bed'
            }
            if (!Schema::hasColumn('booking_hotels', 'no_of_beds')) {
                $table->integer('no_of_beds')->default(0)->after('no_of_rooms');
            }
            if (!Schema::hasColumn('booking_hotels', 'meal_plan')) {
                $table->string('meal_plan', 100)->nullable()->after('gender');
            }
            if (!Schema::hasColumn('booking_hotels', 'rate_per_unit')) {
                $table->decimal('rate_per_unit', 12, 2)->default(0.00)->after('no_of_rooms');
            }
            if (!Schema::hasColumn('booking_hotels', 'amount')) {
                $table->decimal('amount', 14, 2)->default(0.00)->after('rate_per_unit');
            }
            if (!Schema::hasColumn('booking_hotels', 'hijri_check_in')) {
                $table->string('hijri_check_in', 100)->nullable()->after('check_in');
            }
            if (!Schema::hasColumn('booking_hotels', 'hijri_check_out')) {
                $table->string('hijri_check_out', 100)->nullable()->after('check_out');
            }
        });

        // 3. Create hotel_sales table (Sale Invoices to Agents / Clients)
        if (!Schema::hasTable('hotel_sales')) {
            Schema::create('hotel_sales', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number')->unique();
                $table->string('reservation_title')->default('HAJJ RESERVATION 1448/2027');
                $table->string('sale_for')->default('company'); // 'company' (agent) or 'client'
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
                $table->foreignId('booking_id')->nullable()->constrained('bookings')->nullOnDelete();
                $table->string('customer_name');
                $table->string('attn')->nullable();
                $table->string('contact')->nullable();
                $table->date('invoice_date')->nullable();
                $table->string('currency')->default('SAR');
                $table->decimal('total_amount', 14, 2)->default(0.00);
                $table->decimal('till_received', 14, 2)->default(0.00);
                $table->decimal('balance_remaining', 14, 2)->default(0.00);
                $table->text('terms_conditions')->nullable();
                $table->text('cancellation_policy')->nullable();
                $table->text('payment_procedure')->nullable();
                $table->string('ceo_name')->default('MUHAMMAD YOUSUF PIRWANI');
                $table->string('ceo_title')->default('C.E.O,');
                $table->string('status')->default('confirmed');
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 4. Create hotel_sale_items table (Itemized lines: Pullman Zamzam Double, Triple, Nusk Al Madinah Bed, etc.)
        if (!Schema::hasTable('hotel_sale_items')) {
            Schema::create('hotel_sale_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_sale_id')->constrained('hotel_sales')->cascadeOnDelete();
                $table->foreignId('hotel_id')->nullable()->constrained('hotels')->nullOnDelete();
                $table->string('hotel_name');
                $table->string('allotment_basis', 20)->default('room'); // 'room' or 'bed'
                $table->string('room_type')->default('DOUBLE'); // 'DOUBLE', 'TRIPLE', 'QUAD', 'BED', etc.
                $table->integer('quantity')->default(1); // Rooms qty or Beds qty
                $table->integer('male_beds')->default(0);
                $table->integer('female_beds')->default(0);
                $table->string('meal_plan')->nullable()->default('HALF BOARD');
                $table->date('check_in')->nullable();
                $table->date('check_out')->nullable();
                $table->string('hijri_check_in')->nullable(); // e.g. 22-ZILQAIDA
                $table->string('hijri_check_out')->nullable(); // e.g. 01-ZILHAJJ
                $table->integer('no_of_nights')->default(1);
                $table->decimal('rate_per_unit', 12, 2)->default(0.00);
                $table->decimal('amount', 14, 2)->default(0.00);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hotel_sale_items');
        Schema::dropIfExists('hotel_sales');

        Schema::table('booking_hotels', function (Blueprint $table) {
            $table->dropColumn([
                'allotment_basis',
                'no_of_beds',
                'meal_plan',
                'rate_per_unit',
                'amount',
                'hijri_check_in',
                'hijri_check_out'
            ]);
        });

        Schema::table('hotel_room_inventories', function (Blueprint $table) {
            $table->dropColumn([
                'allotment_basis',
                'hijri_check_in',
                'hijri_check_out',
                'no_of_nights',
                'total_cost',
                'total_selling'
            ]);
        });
    }
};
