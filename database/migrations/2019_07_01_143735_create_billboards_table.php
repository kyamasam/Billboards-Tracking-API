<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillboardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billboards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->float('display_duration');
            $table->string('location_name');
            $table->decimal('location_lat', 10, 7);
            $table->decimal('location_long', 10, 7);
            $table->enum('placement', ['left', 'right']);
            $table->string('billboard_picture');
            $table->integer('average_daily_views');
            $table->enum('definition', ['high', 'low']);
            $table->float('dimensions_width');
            $table->float('dimensions_height');
            $table->text('description');
            $table->softDeletes();
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
        Schema::dropIfExists('billboards');
    }
}
