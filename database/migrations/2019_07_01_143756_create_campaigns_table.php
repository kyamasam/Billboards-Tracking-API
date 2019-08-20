<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('campaign_name');
            $table->text('campaign_description')->nullable();
            $table->integer('budget_id')->refernces('id')->on('budgets')->nullable();
            $table->integer('schedule_id')->refernces('id')->on('schedules')->nullable();
            $table->integer('campaign_status')->refernces('id')->on('campaign_statuses')->default(1);
            $table->integer('owner_id')->refernces('id')->on('users');
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
        Schema::dropIfExists('campaigns');
    }
}
