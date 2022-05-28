<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return app()->version();
});


	Route::group(['namespace' => 'App\Http\Controllers\Acl'], function() {
		Route::post('store-url', 'UrlController@storeUrl');
		Route::post('show-url', 'UrlController@showUrl');
		Route::post('track-url', 'UrlController@trackUrl');
		Route::get('short/{url_key}', 'UrlController@visitUrl');
	});