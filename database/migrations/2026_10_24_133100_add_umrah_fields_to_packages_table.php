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
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'package_type')) {
                $table->enum('package_type', ['hajj', 'umrah', 'international', 'domestic'])
                    ->default('hajj')
                    ->after('id');
            }
            if (!Schema::hasColumn('packages', 'status')) {
                $table->enum('status', ['active', 'inactive', 'draft'])
                    ->default('active')
                    ->after('package_type');
            }
            if (!Schema::hasColumn('packages', 'package_code')) {
                $table->string('package_code', 50)->nullable()->after('code');
            }
            if (!Schema::hasColumn('packages', 'is_group_package')) {
                $table->boolean('is_group_package')->default(false)->after('package_code');
            }
            if (!Schema::hasColumn('packages', 'visa_status')) {
                $table->enum('visa_status', ['included', 'excluded'])->default('included')->after('is_group_package');
            }
            if (!Schema::hasColumn('packages', 'is_dual_country')) {
                $table->boolean('is_dual_country')->default(false)->after('visa_status');
            }
            if (!Schema::hasColumn('packages', 'price_sharing')) {
                $table->decimal('price_sharing', 12, 2)->default(0)->after('is_dual_country');
            }
            if (!Schema::hasColumn('packages', 'price_quad')) {
                $table->decimal('price_quad', 12, 2)->default(0)->after('price_sharing');
            }
            if (!Schema::hasColumn('packages', 'price_triple')) {
                $table->decimal('price_triple', 12, 2)->default(0)->after('price_quad');
            }
            if (!Schema::hasColumn('packages', 'price_double')) {
                $table->decimal('price_double', 12, 2)->default(0)->after('price_triple');
            }
            if (!Schema::hasColumn('packages', 'duration_pricing_options')) {
                $table->json('duration_pricing_options')->nullable()->after('price_double');
            }
            if (!Schema::hasColumn('packages', 'key_features')) {
                $table->longText('key_features')->nullable()->after('duration_pricing_options');
            }
            if (!Schema::hasColumn('packages', 'inclusions')) {
                $table->longText('inclusions')->nullable()->after('key_features');
            }
            if (!Schema::hasColumn('packages', 'exclusions')) {
                $table->longText('exclusions')->nullable()->after('inclusions');
            }
            if (!Schema::hasColumn('packages', 'requirements_text')) {
                $table->longText('requirements_text')->nullable()->after('exclusions');
            }
            if (!Schema::hasColumn('packages', 'package_image')) {
                $table->string('package_image', 255)->nullable()->after('requirements_text');
            }
            if (!Schema::hasColumn('packages', 'valid_from')) {
                $table->date('valid_from')->nullable()->after('package_image');
            }
            if (!Schema::hasColumn('packages', 'valid_until')) {
                $table->date('valid_until')->nullable()->after('valid_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach ([
                'package_type',
                'status',
                'package_code',
                'is_group_package',
                'visa_status',
                'is_dual_country',
                'price_sharing',
                'price_quad',
                'price_triple',
                'price_double',
                'duration_pricing_options',
                'key_features',
                'inclusions',
                'exclusions',
                'requirements_text',
                'package_image',
                'valid_from',
                'valid_until',
            ] as $column) {
                if (Schema::hasColumn('packages', $column)) {
                    $columnsToDrop[] = $column;
                }
            }

            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
