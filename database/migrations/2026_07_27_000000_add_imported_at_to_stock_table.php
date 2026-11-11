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
                if (!Schema::hasColumn('stock', 'imported_at')) {
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
            });
        }
    }
};
