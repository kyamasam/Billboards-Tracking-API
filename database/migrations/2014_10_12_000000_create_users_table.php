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
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('user_name')->unique();
            $table->string('last_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('msisdn')->unique();
            $table->integer('account_type')->nullable()->default(1);
            $table->string('avatar')->default('/images/default_avatar.png');
            $table->string('cover_photo')->nullable();
            $table->integer('is_verified')->nullable()->default(0);
            $table->integer('is_trusted')->nullable()->default(0);
            $table->integer('account_status')->nullable()->default(1);
            $table->string('password');
            $table->softDeletes();
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
