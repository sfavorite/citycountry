<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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


Log::info('API requested');
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/cities', 'ResponseController@getCities');
Route::get('/countries', 'ResponseController@getCountries');
Route::get('/citycountry', 'ResponseController@getCityCountry');
Route::get('/latlong', 'ResponseController@getLatLong');
