<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('user_name')->unique();
            $table->string('last_name');
            $table->string('middle_name');
            $table->string('first_name');
            $table->string('msisdn');
            $table->integer('user_type');
            $table->string('avatar')->default('/images/default_avatar.png');
            $table->string('cover_photo');
            $table->integer('is_verified')->default(0);
            $table->integer('is_trusted')->default(0);
            $table->integer('account_status')->default(1);
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
