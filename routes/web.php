<?php

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

\Debugbar::info('Have a web route');

Route::get('/index', function () {
    \Debugbar::info('Returning index view');
    return view('index');
});
