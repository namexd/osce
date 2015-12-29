<?php

Route::group(['prefix' => 'osce', 'namespace' => 'Modules\Osce\Http\Controllers'], function()
{
	Route::get('/', 'OsceController@index');
	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
		Route::get('invigilator/invigilator-list', 	['uses'=>'InvigilatorController@getInvigilatorList','as'=>'osce.admin.invigilator.getInvigilatorList']);
		Route::get('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@getAddInvigilator','as'=>'osce.admin.invigilator.getAddInvigilator']);
		Route::post('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@postAddInvigilator','as'=>'osce.admin.invigilator.postAddInvigilator']);
	});
});