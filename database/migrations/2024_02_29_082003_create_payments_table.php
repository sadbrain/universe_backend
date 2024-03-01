<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string("payment_status",100)->default("pending");
            $table->string("session_id",255)->nullable();
            $table->string("payment_intent_id",255)->nullable();
            $table->dateTime("payment_date")->nullable()->default(now());
            $table->date("payment_due_date")->nullable();
            $table->unsignedTinyInteger('user_id')->nullable()->default(null);
            $table->unsignedTinyInteger('order_id')->default(null);
            $table->softDeletes();
            $table->timestamps();
            $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
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
        Schema::dropIfExists('payments');
    }
}
