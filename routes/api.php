<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix'=>'v1','as'=>'v1.'], function() {


    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/register', 'AuthController@register');
    //todo: make all the routes plural
    //routes that require authentication
    Route::middleware('auth:api')->group(function () {


        Route::get('auth/details', 'AuthController@details');
        Route::resource('account', 'UserAccountManagementController');
        Route::post('account/bulk_delete', 'UserAccountManagementController@bulk_delete');
        Route::put('account/update/{id}', 'UserAccountManagementController@admin_update');

        Route::resource('billboards', 'BillboardController');
        Route::resource('campaigns/budgets', 'BudgetController');
        Route::resource('campaigns/schedules', 'ScheduleController');
        Route::resource('campaigns/status', 'CampaignStatusController');
        Route::resource('campaigns/artwork', 'ArtworkController');
        Route::resource('campaigns', 'CampaignController');
        Route::post('campaigns/locations', 'CampaignController@Locations');
        Route::get('campaigns/locations/{campaign_id}', 'CampaignController@SelectedLocations');
        Route::delete('campaigns/locations/remove_selections', 'CampaignController@removeSelections');

        //PAYMENTS
        Route::get('payments/stk/{phone}/{paybill?}', 'StkPushContoller@index');

    });





});


