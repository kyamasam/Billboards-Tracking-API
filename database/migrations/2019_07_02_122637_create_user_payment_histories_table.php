<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPaymentHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_payment_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('provider_id')->references('id')->on('payment_providers');
            $table->float('amount_paid');
            $table->string('msisdn');
            $table->integer('user_id')->references('id')->on('users');
            $table->string('confirmation_receipt', 10); //todo :: confirm the length of the receipt number
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
        Schema::dropIfExists('user_payment_histories');
    }
}
