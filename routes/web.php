<?php

use \App\City;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Log::info('Web requested');
Route::get('/', function () {
    return view('index');
});

Route::get('/geo', function() {
    $cities = City::first();
    return $cities;
});
