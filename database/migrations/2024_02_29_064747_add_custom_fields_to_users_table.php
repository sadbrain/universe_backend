<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable();
            $table->string('street_address', 100)->nullable();
            $table->string('district_address', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('avatar', 255)->nullable()->default("https://st.quantrimang.com/photos/image/072015/22/avatar.jpg");
            $table->unsignedTinyInteger('role_id')->nullable()->default(null);
            $table->unsignedTinyInteger('company_id')->nullable()->default(null);
            $table->softDeletes();
            $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('set null')
                    ->onUpdate('CASCADE');
            $table->foreign('company_id')
                    ->references('id')
                    ->on('companies')
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
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
