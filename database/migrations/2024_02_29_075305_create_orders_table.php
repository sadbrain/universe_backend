<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 100);
            $table->string('phone', 20);
            $table->string('street_address', 100);
            $table->string('district_address', 100);
            $table->string('city', 100);
            $table->dateTime("order_date")->default(now());
            $table->string("order_status",100)->default("pending");
            $table->unsignedDouble("order_total",14,2)->default(0.0);
            $table->dateTime("shipping_date")->nullable();
            $table->string('tracking_number', 255)->nullable()->default(null);
            $table->string('carrier', 255)->nullable()->default(null);
            $table->unsignedTinyInteger('user_id')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null')
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
        Schema::dropIfExists('orders');
    }
}
