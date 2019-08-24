<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArtworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('artworks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('height');
            $table->float('width');
            $table->string('file_type')->nullable();
            $table->float('campaign_id')->references('id')->on('campaigns')->nullable();
            $table->float('billboard_id')->references('id')->on('billboards')->nullable();
            $table->string('image_src');
            $table->boolean('animate')->default(false);
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
        Schema::dropIfExists('artworks');
    }
}
