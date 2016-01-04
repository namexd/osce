<?php

Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
	Route::get('admin/index', 'OsceController@index');
    Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
		//房间
        Route::controller('room','RoomController');
        Route::get('room/room-list', ['uses'=>'RoomController@getRoomList','as'=>'osce.admin.room.getRoomList']);  //列表的着陆页
		Route::get('room/edit-room', ['uses'=>'RoomController@getEditRoom','as'=>'osce.admin.room.getEditRoom']);  //修改的着陆页

		Route::post('room/edit-room', ['uses'=>'RoomController@postEditRoom','as'=>'osce.admin.room.postEditRoom']);   //修改的业务逻辑
		Route::post('room/create-room', ['uses'=>'RoomController@postCreateRoom','as'=>'osce.admin.room.postCreateRoom']);  //添加的业务逻辑

		Route::controller('case','CaseController');
		Route::get('case/case-list', ['uses'=>'CaseController@getCaseList','as'=>'osce.admin.case.getCaseList']);  //病例的着陆页
		Route::get('case/edit-case', ['uses'=>'CaseController@getEditCase','as'=>'osce.admin.case.getEditCase']);  //病例的修改页
	});



	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
        //监考老师
		Route::get('invigilator/sp-invigilator-list', 	['uses'=>'InvigilatorController@getSpInvigilatorList','as'=>'osce.admin.invigilator.getSpInvigilatorList']);
		Route::get('invigilator/invigilator-list', 	['uses'=>'InvigilatorController@getInvigilatorList','as'=>'osce.admin.invigilator.getInvigilatorList']);
		Route::get('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@getAddInvigilator','as'=>'osce.admin.invigilator.getAddInvigilator']);
		Route::post('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@postAddInvigilator','as'=>'osce.admin.invigilator.postAddInvigilator']);
		//测试
		Route::get('invigilator/test', 	['uses'=>'InvigilatorController@getTest','as'=>'osce.admin.invigilator.getTest']);

		//设备
		Route::get('machine/category-list', 	['uses'=>'MachineController@getCategoryList','as'=>'osce.admin.machine.getCategoryList']);
		Route::get('machine/machine-list', 	['uses'=>'MachineController@getMachineList','as'=>'osce.admin.machine.getMachineList']);
		Route::post('machine/add-machine', 	['uses'=>'MachineController@postAddMachine','as'=>'osce.admin.machine.postAddMachine']);
		//考场
		Route::get('place/place-list', 	['uses'=>'PlaceController@getPlaceList','as'=>'osce.admin.Place.getPlaceList']);
		Route::get('place/edit-place', 	['uses'=>'PlaceController@getEditPlace','as'=>'osce.admin.Place.getEditPlace']);
		Route::get('place/add-place', 	['uses'=>'PlaceController@getAddPlace','as'=>'osce.admin.Place.getAddPlace']);

		//场所分类
		Route::get('place/place-cate-list',['uses'=>'PlaceController@getPlaceCateList','as'=>'osce.admin.place.getPlaceCateList']);
		Route::get('place/edit-place-cate',['uses'=>'PlaceController@getEditPlaceCate','as'=>'osce.admin.place.getEditPlaceCate']);
		//测试
		Route::get('place/test', 	['uses'=>'PlaceController@getTest','as'=>'osce.admin.Place.getTest']);

	});


});

Route::get('room/createroom', function() {return view('osce::admin.test');});  //添加的着陆页,测试用