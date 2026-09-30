<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'camp_category')) {
                $table->string('camp_category')->nullable()->after('zone'); // Maktab C, Maktab A, Maktab A & C
            }
            if (!Schema::hasColumn('packages', 'camp_zone')) {
                $table->string('camp_zone')->nullable()->after('camp_category'); // Zone 5, Zone 1 or 2
            }
            if (!Schema::hasColumn('packages', 'stay_type')) {
                $table->string('stay_type')->nullable()->after('camp_zone'); // LONG STAY, SHORT STAY
            }
            if (!Schema::hasColumn('packages', 'stay_duration')) {
                $table->string('stay_duration')->nullable()->after('stay_type'); // 29 - 30 DAYS, 21 - 22 DAYS, 16 - 17 DAYS
            }
            if (!Schema::hasColumn('packages', 'departure_date_str')) {
                $table->string('departure_date_str')->nullable()->after('stay_duration');
            }
            if (!Schema::hasColumn('packages', 'arrival_date_str')) {
                $table->string('arrival_date_str')->nullable()->after('departure_date_str');
            }
            if (!Schema::hasColumn('packages', 'departure_sector')) {
                $table->string('departure_sector')->nullable()->default('Karachi to Jeddah')->after('arrival_date_str');
            }
            if (!Schema::hasColumn('packages', 'arrival_sector')) {
                $table->string('arrival_sector')->nullable()->default('Jeddah/Madinah to Karachi')->after('departure_sector');
            }
            if (!Schema::hasColumn('packages', 'hijri_year')) {
                $table->string('hijri_year')->nullable()->default('1448')->after('arrival_sector');
            }
            if (!Schema::hasColumn('packages', 'gregorian_year')) {
                $table->string('gregorian_year')->nullable()->default('2027')->after('hijri_year');
            }

            // Qurbani
            if (!Schema::hasColumn('packages', 'qurbani_status')) {
                $table->string('qurbani_status')->nullable()->default('Not Included (Nusuk Masar)')->after('gregorian_year');
            }
            if (!Schema::hasColumn('packages', 'qurbani_charges')) {
                $table->decimal('qurbani_charges', 12, 2)->nullable()->default(0)->after('qurbani_status');
            }
            if (!Schema::hasColumn('packages', 'qurbani_note')) {
                $table->text('qurbani_note')->nullable()->after('qurbani_charges');
            }

            // Maktab C Pricing
            if (!Schema::hasColumn('packages', 'maktab_c_quad_pkr')) {
                $table->decimal('maktab_c_quad_pkr', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_c_quad_usd', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_c_triple_pkr', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_c_triple_usd', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_c_double_pkr', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_c_double_usd', 12, 2)->nullable()->default(0);
            }

            // Maktab A Pricing
            if (!Schema::hasColumn('packages', 'maktab_a_quad_pkr')) {
                $table->decimal('maktab_a_quad_pkr', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_a_quad_usd', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_a_triple_pkr', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_a_triple_usd', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_a_double_pkr', 12, 2)->nullable()->default(0);
                $table->decimal('maktab_a_double_usd', 12, 2)->nullable()->default(0);
            }

            // Azizia Separate Room Addon Pricing
            if (!Schema::hasColumn('packages', 'azizia_quad_pkr')) {
                $table->decimal('azizia_quad_pkr', 12, 2)->nullable()->default(50000);
                $table->decimal('azizia_quad_usd', 12, 2)->nullable()->default(181);
                $table->decimal('azizia_triple_pkr', 12, 2)->nullable()->default(100000);
                $table->decimal('azizia_triple_usd', 12, 2)->nullable()->default(363);
                $table->decimal('azizia_double_pkr', 12, 2)->nullable()->default(200000);
                $table->decimal('azizia_double_usd', 12, 2)->nullable()->default(727);
            }

            if (!Schema::hasColumn('packages', 'package_included_points')) {
                $table->longText('package_included_points')->nullable();
            }
            if (!Schema::hasColumn('packages', 'instructions_points')) {
                $table->longText('instructions_points')->nullable();
            }
            if (!Schema::hasColumn('packages', 'important_note')) {
                $table->longText('important_note')->nullable();
            }
        });

        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'camp')) {
                $table->string('camp')->nullable()->after('package_year');
            }
            if (!Schema::hasColumn('bookings', 'qurbani_option')) {
                $table->string('qurbani_option')->nullable()->default('not_included')->after('camp');
            }
            if (!Schema::hasColumn('bookings', 'qurbani_qty')) {
                $table->integer('qurbani_qty')->nullable()->default(0)->after('qurbani_option');
            }
            if (!Schema::hasColumn('bookings', 'qurbani_charges')) {
                $table->decimal('qurbani_charges', 10, 2)->nullable()->default(0)->after('qurbani_qty');
            }
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn([
                'camp_category', 'camp_zone', 'stay_type', 'stay_duration',
                'departure_date_str', 'arrival_date_str', 'departure_sector', 'arrival_sector',
                'hijri_year', 'gregorian_year', 'qurbani_status', 'qurbani_charges', 'qurbani_note',
                'maktab_c_quad_pkr', 'maktab_c_quad_usd', 'maktab_c_triple_pkr', 'maktab_c_triple_usd', 'maktab_c_double_pkr', 'maktab_c_double_usd',
                'maktab_a_quad_pkr', 'maktab_a_quad_usd', 'maktab_a_triple_pkr', 'maktab_a_triple_usd', 'maktab_a_double_pkr', 'maktab_a_double_usd',
                'azizia_quad_pkr', 'azizia_quad_usd', 'azizia_triple_pkr', 'azizia_triple_usd', 'azizia_double_pkr', 'azizia_double_usd',
                'package_included_points', 'instructions_points', 'important_note',
            ]);
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['camp', 'qurbani_option', 'qurbani_qty', 'qurbani_charges']);
        });
    }
};
