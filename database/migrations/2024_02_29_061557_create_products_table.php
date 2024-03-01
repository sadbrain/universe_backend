<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->string('thumbnail', 255)->nullable()->default("https://placehold.co/500x600/png");
            $table->text('description')->nullable();
            $table->unsignedDouble('price',12,2)->default(0.0);
            $table->unsignedInteger('rating')->default(0);
            $table->unsignedInteger('favorites')->default(0);
            $table->unsignedTinyInteger('category_id')->nullable()->default(null);
            $table->unsignedTinyInteger('inventory_id')->nullable()->default(null);
            $table->unsignedTinyInteger('discount_id')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('set null')
                    ->onUpdate('CASCADE');
            $table->foreign('discount_id')
                    ->references('id')
                    ->on('discounts')
                    ->onDelete('set null')
                    ->onUpdate('CASCADE');
            $table->foreign("inventory_id")
                    ->references("id")
                    ->on("inventories")
                    ->onDelete('set null')
                    ->onUpdate('CASCADE');/// Hoặc 'CASCADE' tùy thuộc vào yêu cầu của bạn
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
