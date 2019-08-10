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


    //password resets

    Route::post('auth/password/create', 'PasswordResetController@create');
    Route::get('auth/password/find/{token}', 'PasswordResetController@find');
    Route::post('auth/password/reset', 'PasswordResetController@reset');

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
        Route::get('campaigns/filter/{status}', 'CampaignController@campaignsFiltered');
        Route::post('campaigns/update_status/{id}', 'CampaignController@updateCampaignStatus');
        Route::post('campaigns/locations', 'CampaignController@Locations');
        Route::get('campaigns/locations/{campaign_id}', 'CampaignController@SelectedLocations');
        Route::delete('campaigns/locations/remove_selections', 'CampaignController@removeSelections');

        //PAYMENTS
        //payment providers
        Route::resource('payments','PaymentProvidersController');
        Route::post('payments/stk/', 'MpesaStkTriggerController@index');
        Route::post('payments/stk/verify', 'MpesaStkTriggerController@verify');
        Route::get('payments/stk/try_email', 'MpesaStkTriggerController@EmailTry');

        //user wallet
        Route::get('wallets','WalletController@index');
        Route::get('wallets/transactions','WalletController@transactions');

    });

});


