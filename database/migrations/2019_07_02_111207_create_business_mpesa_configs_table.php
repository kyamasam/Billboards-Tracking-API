<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessMpesaConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_mpesa_configs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('mpesa_consumer_key');
            $table->string('mpesa_consumer_secret');
            $table->string('mpesa_short_code');
            $table->string('initiator_name');
            $table->string('initiator_password');
            $table->string('msisdn');
            $table->string('lipa_na_mpesa_online_shortcode');
            $table->string('lipa_na_mpesa_online_passkey');
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
        Schema::dropIfExists('business_mpesa_configs');
    }
}
