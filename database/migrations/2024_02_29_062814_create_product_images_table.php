<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 255)->nullable();
            $table->string('url', 255)->nullable();
            $table->unsignedTinyInteger('product_id')->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
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
        Schema::dropIfExists('product_images');
    }
}
