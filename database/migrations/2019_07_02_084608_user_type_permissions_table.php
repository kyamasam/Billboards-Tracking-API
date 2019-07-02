<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserTypePermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('user_type_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('user_type_id')->references('id')->on('user_types');
            $table->integer('permission_id')->references('id')->on('permissions');
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
         Schema::dropIfExists('user_type_permissions');
    }
}
