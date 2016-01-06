<?php
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers'], function () {
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
		Route::get('login/index', ['uses' => 'LoginController@getIndex', 'as' => 'osce.admin.getIndex']);
		Route::post('login/index', ['uses' => 'LoginController@postIndex', 'as' => 'osce.admin.postIndex']);
	});
});
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
	Route::get('admin/index', ['uses'=>'OsceController@index','as'=>'osce.admin.index']);
	Route::get('/index', 'OsceController@index');
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
		Route::get('case/create-case', ['uses'=>'CaseController@getCreateCase','as'=>'osce.admin.case.getCreateCase']);  //病例的添加页
	});



	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
        //监考老师

		Route::get('invigilator/invigilator-list', 	['uses'=>'InvigilatorController@getInvigilatorList','as'=>'osce.admin.invigilator.getInvigilatorList']);
		Route::get('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@getAddInvigilator','as'=>'osce.admin.invigilator.getAddInvigilator']);
		Route::post('invigilator/add-invigilator', 	['uses'=>'InvigilatorController@postAddInvigilator','as'=>'osce.admin.invigilator.postAddInvigilator']);

		Route::get('invigilator/edit-invigilator', 	['uses'=>'InvigilatorController@getEditInvigilator','as'=>'osce.admin.invigilator.getEditInvigilator']);
		Route::post('invigilator/edit-invigilator', ['uses'=>'InvigilatorController@postEditInvigilator','as'=>'osce.admin.invigilator.postEditInvigilator']);

		Route::get('invigilator/sp-invigilator-list', 	['uses'=>'InvigilatorController@getSpInvigilatorList','as'=>'osce.admin.invigilator.getSpInvigilatorList']);
		Route::get('invigilator/add-sp-invigilator',['uses'=>'InvigilatorController@getAddSpInvigilator','as'=>'osce.admin.invigilator.getAddSpInvigilator']);
		Route::get('invigilator/edit-sp-invigilator',['uses'=>'InvigilatorController@getEditSpInvigilator','as'=>'osce.admin.invigilator.getEditSpInvigilator']);
		Route::post('invigilator/add-sp-invigilator', 	['uses'=>'InvigilatorController@postAddSpInvigilator','as'=>'osce.admin.invigilator.postAddSpInvigilator']);
		Route::post('invigilator/edit-sp-invigilator', 	['uses'=>'InvigilatorController@postEditSpInvigilator','as'=>'osce.admin.invigilator.postEditSpInvigilator']);




		Route::get('invigilator/del-invitation', 	['uses'=>'InvigilatorController@getDelInvitation','as'=>'osce.admin.invigilator.getDelInvitation']);
		//测试
		Route::get('invigilator/test', 	['uses'=>'InvigilatorController@getTest','as'=>'osce.admin.invigilator.getTest']);

		//设备
		Route::get('machine/category-list', 	['uses'=>'MachineController@getCategoryList','as'=>'osce.admin.machine.getCategoryList']);
		Route::get('machine/machine-list', 	['uses'=>'MachineController@getMachineList','as'=>'osce.admin.machine.getMachineList']);
		Route::post('machine/add-machine', 	['uses'=>'MachineController@postAddMachine','as'=>'osce.admin.machine.postAddMachine']);
		Route::post('machine/edit-machine', 	['uses'=>'MachineController@postEditMachine','as'=>'osce.admin.machine.postEditMachine']);
		Route::get('machine/add-cameras', 	['uses'=>'MachineController@getAddCameras','as'=>'osce.admin.machine.getAddCameras']);
		Route::get('machine/edit-cameras', 	['uses'=>'MachineController@getEditCameras','as'=>'osce.admin.machine.getEditCameras']);

		Route::get('machine/add-pad', 	['uses'=>'MachineController@getAddPad','as'=>'osce.admin.machine.getAddPad']);
		Route::get('machine/edit-pad', 	['uses'=>'MachineController@getEditPad','as'=>'osce.admin.machine.getEditPad']);

		Route::get('machine/add-watch', ['uses'=>'MachineController@getAddWatch','as'=>'osce.admin.machine.getAddWatch']);
		Route::get('machine/edit-watch', 	['uses'=>'MachineController@getEditWatch','as'=>'osce.admin.machine.getEditWatch']);


		//考核点
		Route::get('topic/list', 	['uses'=>'TopicController@getList','as'=>'osce.admin.topic.getList']);
		Route::get('topic/add-topic', 	['uses'=>'TopicController@getAddTopic','as'=>'osce.admin.topic.getAddTopic']);
		Route::post('topic/add-topic', 	['uses'=>'TopicController@postAddTopic','as'=>'osce.admin.topic.postAddTopic']);
		Route::get('topic/edit-topic', 	['uses'=>'TopicController@getEditTopic','as'=>'osce.admin.topic.getEditTopic']);
		Route::post('topic/edit-topic', 	['uses'=>'TopicController@postEditTopic','as'=>'osce.admin.topic.postEditTopic']);

		//病例
		Route::post('/case/delete', 	['uses'=>'CaseController@postDelete','as'=>'osce.admin.case.postDelete']);


		//考站
		Route::get('station/station-list', 	['uses'=>'StationController@getStationList','as'=>'osce.admin.Station.getStationList']);
		Route::get('station/edit-station', 	['uses'=>'StationController@getEditStation','as'=>'osce.admin.Station.getEditStation']);
		Route::get('station/add-station', 	['uses'=>'StationController@getAddStation','as'=>'osce.admin.Station.getAddStation']);
		Route::post('station/add-station', ['uses'=>'StationController@postAddStation','as'=>'osce.admin.Station.postAddStation']);
		Route::post('station/delete-station', ['uses'=>'StationController@postDelete','as'=>'osce.admin.Station.postDelete']);


		//场所分类
		Route::get('room-cate/room-cate-list',['uses'=>'RoomCateController@getRoomCateList','as'=>'osce.admin.room.getRoomCateList']);
		Route::get('room-cate/edit-room-cate',['uses'=>'RoomCateController@getEditRoomCate','as'=>'osce.admin.room.getEditRoomCate']);

		//考场
		Route::post('room/delete',['uses'=>'RoomController@postDelete','as'=>'osce.admin.room.postDelete']);

		//用户管理
		Route::get('user/staff-list', 	['uses'=>'UserController@getStaffList','as'=>'osce.admin.user.getStaffList']);
		Route::get('user/edit-staff', 	['uses'=>'UserController@getEditStaff','as'=>'osce.admin.user.getEditStaff']);

		//考试
		Route::get('exam/exam-list', 	['uses'=>'ExamController@getExamList','as'=>'osce.admin.exam.getExamList']);
		Route::get('exam/delete', 	['uses'=>'ExamController@postDelete','as'=>'osce.admin.exam.postDelete']);
		Route::get('exam/add-exam', 	['uses'=>'ExamController@getAddExam','as'=>'osce.admin.exam.getAddExam']);		//新增考试
		Route::post('exam/add-exam', 	['uses'=>'ExamController@postAddExam','as'=>'osce.admin.exam.postAddExam']);
		Route::get('exam/student-list', 	['uses'=>'ExamController@getStudentList','as'=>'osce.admin.exam.getStudentList']);

		Route::post('exam/delete', 	['uses'=>'ExamController@postDelete','as'=>'osce.admin.exam.postDelete']);
		Route::get('exam/station-list', ['uses'=>'ExamController@getStationList','as'=>'osce.admin.exam.getStationList']);

		//sp
		Route::get('/spteacher/show', ['uses'=>'SpteacherController@getStationList','as'=>'osce.admin.spteacher.getShow']);


		//通知
		Route::get('notice/msg', 	['uses'=>'NoticeController@getMsg','as'=>'osce.admin.notice.getMsg']);
		Route::get('notice/list', 	['uses'=>'NoticeController@getList','as'=>'osce.admin.notice.getList']);
		Route::get('notice/add-notice', 	['uses'=>'NoticeController@getAddNotice','as'=>'osce.admin.notice.getAddNotice']);
		Route::post('notice/add-notice', 	['uses'=>'NoticeController@postAddNotice','as'=>'osce.admin.notice.postAddNotice']);

		Route::get('notice/edit-notice', 	['uses'=>'NoticeController@getEditNotice','as'=>'osce.admin.notice.getEditNotice']);
		Route::get('notice/del-notice', 	['uses'=>'NoticeController@getDelNotice','as'=>'osce.admin.notice.getDelNotice']);
		Route::post('notice/edit-notice', 	['uses'=>'NoticeController@postEditNotice','as'=>'osce.admin.notice.postEditNotice']);


		//测试
		Route::get('station/test', 	['uses'=>'StationController@getTest','as'=>'osce.admin.Station.getTest']);

	});


});

Route::get('room/createroom', function() {return view('osce::admin.test');});  //添加的着陆页,测试用
