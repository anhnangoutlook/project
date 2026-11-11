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
        if (Schema::hasTable('stocks') && !Schema::hasTable('stock')) {
            Schema::rename('stocks', 'stock');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('stock') && !Schema::hasTable('stocks')) {
            Schema::rename('stock', 'stocks');
        }
    }
};
