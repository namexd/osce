<?php

Route::group(['prefix' => 'osce', 'namespace' => 'Modules\Osce\Http\Controllers'], function()
{
	Route::get('/', 'OsceController@index');
});