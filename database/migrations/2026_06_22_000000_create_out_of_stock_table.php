<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('out_of_stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('category')->nullable()->index();
            $table->string('name')->nullable();
            $table->string('code_from')->nullable();
            $table->string('code_to')->nullable();

            $table->integer('total_qty')->default(0);
            $table->integer('removed_qty')->default(0);
            $table->integer('remain_qty')->default(0)->index();

            $table->decimal('total_weight', 12, 3)->default(0);
            $table->decimal('removed_weight', 12, 3)->default(0);
            $table->decimal('remain_weight', 12, 3)->default(0)->index();

            $table->unsignedBigInteger('store_id')->nullable()->index();
            $table->integer('threshold')->default(3);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('out_of_stock');
    }
};
