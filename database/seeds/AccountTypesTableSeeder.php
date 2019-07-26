<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AccountTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('account_types')->insert(
            [
                'name'=>'Normal',
                'description'=>'Normal user. Has lowest access Level',
            ]
        );
        DB::table('account_types')->insert(
            [
                'name'=>'Admin',
                'description'=>'Administrator. Has highest access Level',
            ]
        );
    }
}
