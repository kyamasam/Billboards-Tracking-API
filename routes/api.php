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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix'=>'v1','as'=>'v1.'], function() {

    Route::post('users/login', 'AuthController@login');
    Route::post('users/register', 'AuthController@register');
});

Route::middleware('auth:api')->group(function () {
//    Route::get('user', 'PassportController@details');

    //other routes that require authentication
//    Route::resource('products', 'ProductController');
});
