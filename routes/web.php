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

// 版本更新说明
Route::get('/v', function () {
    return view('welcome');
});

Route::get('/{short}', 'ShortUrlController@shorttolong');

Route::post('/l/longtoshort', 'ShortUrlController@longtoshort');

