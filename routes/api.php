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



    Route::post('password_reset', 'PasswordResetController@create');
    Route::get('password_reset/find/{token}', 'PasswordResetController@find');
    Route::post('password_reset/complete', 'PasswordResetController@reset');
    Route::post('account/confirm_email', 'UserAccountManagementController@confirm_email');
    Route::post('account/confirm_email_complete', 'UserAccountManagementController@confirm_email_complete');
    //routes that require authentication

    Route::middleware('auth:api')->group(function () {
         Route::get('auth/details', 'AuthController@details');
         Route::get('account/account_types', 'UserAccountManagementController@account_types');
        Route::resource('account', 'UserAccountManagementController');
        Route::post('account/bulk_delete', 'UserAccountManagementController@bulk_delete');
        Route::put('account/update/{id}', 'UserAccountManagementController@admin_update');
        Route::post('account/change_phone_number/{id}', 'UserAccountManagementController@change_phone_number');
        Route::post('account/change_password/', 'UserAccountManagementController@change_password');

        Route::post('account/change_email/{id}', 'UserAccountManagementController@change_email');
        Route::post('account/verify_phone', 'PhoneNumberVerificationController@send_code');
        Route::post('account/verify_phone/complete', 'PhoneNumberVerificationController@send_code_complete');



        Route::resource('billboards', 'BillboardController');
        Route::post('billboards/{id}/update_image', 'BillboardController@update_image');
        Route::resource('campaigns/budgets', 'BudgetController');
        Route::resource('campaigns/schedules', 'ScheduleController');
        Route::resource('campaigns/status', 'CampaignStatusController');
        Route::resource('campaigns/artwork', 'ArtworkController');
        Route::post('campaigns/artwork/{id}/update_image', 'ArtworkController@update_image');
        Route::resource('campaigns', 'CampaignController');
        Route::get('campaigns/filter/{status}', 'CampaignController@campaignsFiltered');
        Route::get('campaigns/users/{id}', 'CampaignController@CampaignByUserId');
        Route::post('campaigns/days/{start_date}/{end_date}', 'CampaignController@campaignsDaysFiltered');
        Route::post('campaigns/update_status/{id}', 'CampaignController@updateCampaignStatus');
        Route::post('campaigns/locations', 'CampaignController@Locations');
        Route::get('campaigns/locations/{campaign_id}', 'CampaignController@SelectedLocations');
        Route::delete('campaigns/locations/remove_selections', 'CampaignController@removeSelections');

        //bulk create campaigns
        Route::post('campaigns/bulk_create', 'CampaignController@bulk_create');

        //PAYMENTS
        //payment providers
        Route::resource('payments','PaymentProvidersController');
        Route::post('payments/stk/', 'MpesaStkTriggerController@index');
        Route::post('payments/stk/verify', 'MpesaStkTriggerController@verify');
        Route::get('wallets/payments/transactions','WalletController@AllTransactions');

        //user wallet
        Route::get('wallets','WalletController@index');
        Route::get('wallets/transactions','WalletController@transactions');

    });



});


