<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMpesaStkTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mpesa_stk_triggers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('merchant_request_id');
            $table->string('checkout_request_id');
            $table->string('response_code');
            $table->text('response_description');
            $table->text('customer_message');
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
        Schema::dropIfExists('mpesa_stk_triggers');
    }
}
