<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BillboardCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('billboard_campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('billboard_id')->refernces('id')->on('billboards');
            $table->integer('campaign_id')->refernces('id')->on('campaigns');
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
         Schema::dropIfExists('billboard_campaigns');
    }
}
