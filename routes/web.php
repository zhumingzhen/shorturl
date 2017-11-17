<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/', 'ShortUrlController@index');

Route::any('/{short}', 'ShortUrlController@shorttolong');

Route::any('/l/longtoshort', 'ShortUrlController@longtoshort');
