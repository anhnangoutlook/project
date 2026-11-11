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
        Schema::table('stocks', function (Blueprint $table) {
            if (!Schema::hasColumn('stocks', 'code')) {
                $table->string('code')->nullable()->after('id');
            }
            if (!Schema::hasColumn('stocks', 'name')) {
                $table->string('name')->nullable()->after('code');
            }
            if (!Schema::hasColumn('stocks', 'weight')) {
                $table->string('weight')->nullable()->after('name');
            }
            if (!Schema::hasColumn('stocks', 'qty1')) {
                $table->integer('qty1')->default(0)->after('weight');
            }
            if (!Schema::hasColumn('stocks', 'imported_at')) {
                $table->timestamp('imported_at')->nullable()->after('qty1');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            if (Schema::hasColumn('stocks', 'imported_at')) {
                $table->dropColumn('imported_at');
            }
            if (Schema::hasColumn('stocks', 'qty1')) {
                $table->dropColumn('qty1');
            }
            if (Schema::hasColumn('stocks', 'weight')) {
                $table->dropColumn('weight');
            }
            if (Schema::hasColumn('stocks', 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn('stocks', 'code')) {
                $table->dropColumn('code');
            }
        });
    }
};
