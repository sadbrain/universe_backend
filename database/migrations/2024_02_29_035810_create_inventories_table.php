<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('quantity_sold')->default(0);
            $table->string('color', 50)->nullable();
            $table->string('size', 50)->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('inventories');
    }
}
