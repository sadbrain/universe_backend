<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('color', 50);
            $table->string('size', 50);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedDouble("price", 12, 2)->default(0.0);
            $table->unsignedTinyInteger('product_id')->nullable()->default(null);
            $table->unsignedTinyInteger('order_id')->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('set null')
                    ->onUpdate('CASCADE');
            $table->foreign('order_id')
                    ->references('id')
                    ->on('orders')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_details');
    }
}
