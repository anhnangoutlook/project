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
        if (Schema::hasTable('stock')) {
            Schema::table('stock', function (Blueprint $table) {
                if (!Schema::hasColumn('stock', 'code')) {
                    $table->string('code')->nullable()->after('id');
                }
                if (!Schema::hasColumn('stock', 'name')) {
                    $table->string('name')->nullable()->after('code');
                }
                if (!Schema::hasColumn('stock', 'weight')) {
                    $table->string('weight')->nullable()->after('name');
                }
                if (!Schema::hasColumn('stock', 'qty1')) {
                    $table->integer('qty1')->default(0)->after('weight');
                }
                if (!Schema::hasColumn('stock', 'imported_at')) {
                    $table->timestamp('imported_at')->nullable()->after('qty1');
                }
            });
        }

        if (Schema::hasTable('good_in_stock')) {
            Schema::table('good_in_stock', function (Blueprint $table) {
                if (!Schema::hasColumn('good_in_stock', 'code')) {
                    $table->string('code')->nullable()->after('id');
                }
                if (!Schema::hasColumn('good_in_stock', 'name')) {
                    $table->string('name')->nullable()->after('code');
                }
                if (!Schema::hasColumn('good_in_stock', 'weight')) {
                    $table->string('weight')->nullable()->after('name');
                }
                if (!Schema::hasColumn('good_in_stock', 'qty1')) {
                    $table->integer('qty1')->default(0)->after('weight');
                }
                if (!Schema::hasColumn('good_in_stock', 'imported_at')) {
                    $table->timestamp('imported_at')->nullable()->after('qty1');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock')) {
            Schema::table('stock', function (Blueprint $table) {
                if (Schema::hasColumn('stock', 'imported_at')) {
                    $table->dropColumn('imported_at');
                }
                if (Schema::hasColumn('stock', 'qty1')) {
                    $table->dropColumn('qty1');
                }
                if (Schema::hasColumn('stock', 'weight')) {
                    $table->dropColumn('weight');
                }
                if (Schema::hasColumn('stock', 'name')) {
                    $table->dropColumn('name');
                }
                if (Schema::hasColumn('stock', 'code')) {
                    $table->dropColumn('code');
                }
            });
        }
    }
};
