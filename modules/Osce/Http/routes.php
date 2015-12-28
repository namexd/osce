<?php

Route::group(['prefix' => 'osce', 'namespace' => 'Modules\Osce\Http\Controllers'], function () {
    Route::get('/', 'OsceController@index');
});

Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::controller('place','PlaceController');
        Route::get('place/place-list',['uses'=>'PlaceController@getPlaceList','as'=>'osce.admin.place.getPlaceList']);
    });
});