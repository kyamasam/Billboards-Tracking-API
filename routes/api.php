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

    Route::middleware('auth:api')->group(function () {
        Route::get('auth/details', 'AuthController@details');

        Route::resource('account', 'UserAccountManagementController');
    });



    //routes that require authentication

});


