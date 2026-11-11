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
                if (!Schema::hasColumn('stock', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('stock', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('good_in_stock')) {
            Schema::table('good_in_stock', function (Blueprint $table) {
                if (!Schema::hasColumn('good_in_stock', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('good_in_stock', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
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
                if (Schema::hasColumn('stock', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
                if (Schema::hasColumn('stock', 'created_at')) {
                    $table->dropColumn('created_at');
                }
            });
        }

        if (Schema::hasTable('good_in_stock')) {
            Schema::table('good_in_stock', function (Blueprint $table) {
                if (Schema::hasColumn('good_in_stock', 'updated_at')) {
                    $table->dropColumn('updated_at');
                }
                if (Schema::hasColumn('good_in_stock', 'created_at')) {
                    $table->dropColumn('created_at');
                }
            });
        }
    }
};
