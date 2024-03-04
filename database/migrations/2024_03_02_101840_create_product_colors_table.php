<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_colors', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string("name",100)->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->timestamps();
            $table->unsignedTinyInteger('inventory_id')->nullable()->default(null);
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
        Schema::dropIfExists('product_colors');
    }
}
