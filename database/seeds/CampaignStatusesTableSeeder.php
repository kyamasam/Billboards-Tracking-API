<?php

use Illuminate\Database\Seeder;

class CampaignStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('campaign_statuses')->insert(
            [
                'name'=>'Unverified',
                'description'=>'This campaign has not been verified by the admin',
            ]
        );
        DB::table('campaign_statuses')->insert(
            [
                'name'=>'Active',
                'description'=>'This campaign is currently running',
            ]
        );
        DB::table('campaign_statuses')->insert(
            [
                'name'=>'Archived',
                'description'=>'This campaign has been completed',
            ]
        );
    }
}
