<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShoppingCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shopping_carts', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('color', 50);
            $table->string('size', 50);
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedTinyInteger('product_id')->default(null);
            $table->unsignedTinyInteger('user_id')->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('CASCADE')
                    ->onUpdate('CASCADE');
        /// Hoặc 'CASCADE' tùy thuộc vào yêu cầu của bạn
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shopping_carts');
    }
}
