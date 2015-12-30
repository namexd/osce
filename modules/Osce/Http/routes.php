<?php

Route::group(['prefix' => 'osce', 'namespace' => 'Modules\Osce\Http\Controllers'], function () {
    Route::get('admin/index', 'OsceController@index');
});

Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
        Route::controller('place','PlaceController');
        Route::get('place/place-list',['uses'=>'PlaceController@getPlaceList','as'=>'osce.admin.place.getPlaceList']);
    });

	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
        //监考老师
		Route::get('invigilator/sp-invigilator-list', 	['uses'=>'InvigilatorController@getSpInvigilatorList','as'=>'osce.admin.invigilator.getSpInvigilatorList']);
		Route::get('invigilator/invigilator-list', 	['uses'=>'InvigilatorController@getInvigilatorList','as'=>'osce.admin.invigilator.getInvigilatorList']);
		Route::get('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@getAddInvigilator','as'=>'osce.admin.invigilator.getAddInvigilator']);
		Route::post('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@postAddInvigilator','as'=>'osce.admin.invigilator.postAddInvigilator']);

        //设备
		Route::get('machine/category-list', 	['uses'=>'MachineController@getCategoryList','as'=>'osce.admin.machine.getCategoryList']);
		//考场
		Route::get('place/edit-place', 	['uses'=>'PlaceController@getEditPlace','as'=>'osce.admin.Place.getEditPlace']);
	});
	//TODO:请前端开发尽早删除这个测试的/test路由。罗海华  2015-12-29 18:31
	Route::get('/test','TestController@test');
});