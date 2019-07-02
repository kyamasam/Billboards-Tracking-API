<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignArtworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_artworks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('campaign_id')->references('id')->on('campaigns');
            $table->integer('artwork_id')->references('id')->on('artworks');
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
        Schema::dropIfExists('campaign_artworks');
    }
}
