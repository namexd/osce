<?php

Route::group(['prefix' => 'osce', 'namespace' => 'Modules\Osce\Http\Controllers'], function()
{
	Route::get('/', 'OsceController@index');
	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
		Route::get('invigilator/sp-invigilator-list', 	['uses'=>'InvigilatorController@getSpInvigilatorList','as'=>'osce.admin.invigilator.getSpInvigilatorList']);
		Route::get('invigilator/invigilator-list', 	['uses'=>'InvigilatorController@getInvigilatorList','as'=>'osce.admin.invigilator.getInvigilatorList']);
		Route::get('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@getAddInvigilator','as'=>'osce.admin.invigilator.getAddInvigilator']);
		Route::post('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@postAddInvigilator','as'=>'osce.admin.invigilator.postAddInvigilator']);
	});
	//TODO:请前端开发尽早删除这个测试的/test路由。罗海华  2015-12-29 18:31
	Route::get('/test','OsceController@test');
});