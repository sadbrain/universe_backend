<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedDouble('price',4,2)->nullable()->default(0.0);
            $table->dateTime("start_date")->nullable()->default(null);
            $table->dateTime("end_date")->nullable()->default(null);
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
        Schema::dropIfExists('discounts');
    }
}
