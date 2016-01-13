<?php
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers'], function () {
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
		Route::get('login/index', ['uses' => 'LoginController@getIndex', 'as' => 'osce.admin.getIndex']);
		Route::post('login/index', ['uses' => 'LoginController@postIndex', 'as' => 'osce.admin.postIndex']);
	});
	Route::group(['prefix' => 'wechat', 'namespace' => 'Wechat'], function () {
		//登录注册
		Route::get('user/register',['uses'=>'UserController@getRegister','as'=>'osce.wechat.user.getRegister']);
		Route::post('user/register',['uses'=>'UserController@postRegister','as'=>'osce.wechat.user.postRegister']);

		Route::get('user/login',['uses'=>'UserController@getLogin','as'=>'osce.wechat.user.getLogin']);
		Route::post('user/login',['uses'=>'UserController@postLogin','as'=>'osce.wechat.user.postLogin']);
		//忘记密码
		Route::get('user/forget-password',['uses'=>'UserController@getForgetPassword','as'=>'osce.wechat.user.getForgetPassword']);
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
		Route::get('room/add-room', ['uses'=>'RoomController@getAddRoom','as'=>'osce.admin.room.getAddRoom']);  //添加的着陆页
		Route::get('room/add-vcr', ['uses'=>'RoomController@getAddVcr','as'=>'osce.admin.room.getAddVcr']);  //房间摄像头的添加

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
		
		Route::get('config/index',  ['uses'=>'ConfigController@getIndex','as'=>'osce.admin.config.getIndex']);
		Route::post('config/store',  ['uses'=>'ConfigController@postStore','as'=>'osce.admin.config.postStore']);


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
		Route::post('topic/import-excel', 	['uses'=>'TopicController@postImportExcel','as'=>'osce.admin.topic.postImportExcel']);


		//病例
		Route::post('case/delete', 	['uses'=>'CaseController@postDelete','as'=>'osce.admin.case.postDelete']);


		//考站
		Route::get('station/station-list', 	['uses'=>'StationController@getStationList','as'=>'osce.admin.Station.getStationList']);
		Route::get('station/edit-station', 	['uses'=>'StationController@getEditStation','as'=>'osce.admin.Station.getEditStation']);
		Route::get('station/add-station', 	['uses'=>'StationController@getAddStation','as'=>'osce.admin.Station.getAddStation']);
		Route::post('station/add-station', ['uses'=>'StationController@postAddStation','as'=>'osce.admin.Station.postAddStation']);
		Route::post('station/delete-station', ['uses'=>'StationController@postDelete','as'=>'osce.admin.Station.postDelete']);
		Route::post('station/edit-station', 	['uses'=>'StationController@postEditStation','as'=>'osce.admin.Station.postEditStation']);


		//场所分类
		Route::get('room-cate/room-cate-list',['uses'=>'RoomCateController@getRoomCateList','as'=>'osce.admin.room.getRoomCateList']);
		Route::get('room-cate/edit-room-cate',['uses'=>'RoomCateController@getEditRoomCate','as'=>'osce.admin.room.getEditRoomCate']);

		//考场
		Route::post('room/delete',['uses'=>'RoomController@postDelete','as'=>'osce.admin.room.postDelete']);
		Route::get('room/room-list',['uses'=>'RoomController@getRoomList','as'=>'osce.admin.room.getRoomList']);

		//用户管理
		Route::get('user/staff-list', 	['uses'=>'UserController@getStaffList','as'=>'osce.admin.user.getStaffList']);
		Route::get('user/edit-staff', 	['uses'=>'UserController@getEditStaff','as'=>'osce.admin.user.getEditStaff']);

		//考试
		Route::get('exam/exam-list', 	['uses'=>'ExamController@getExamList','as'=>'osce.admin.exam.getExamList']);
		Route::get('exam/delete', 	['uses'=>'ExamController@postDelete','as'=>'osce.admin.exam.postDelete']);

		Route::get('exam/add-exam', 	['uses'=>'ExamController@getAddExam','as'=>'osce.admin.exam.getAddExam']);		//新增考试
		Route::post('exam/add-exam', 	['uses'=>'ExamController@postAddExam','as'=>'osce.admin.exam.postAddExam']);
		Route::get('exam/examinee-manage', 	['uses'=>'ExamController@getExamineeManage','as'=>'osce.admin.exam.getExamineeManage']);  //考生管理
		Route::get('exam/del-student', 		['uses'=>'ExamController@getDelStudent','as'=>'osce.admin.exam.getDelStudent']);		//删除考生
		Route::get('exam/add-examinee', 	['uses'=>'ExamController@getAddExaminee','as'=>'osce.admin.exam.getAddExaminee']);		//添加考生
		Route::post('exam/add-examinee', 	['uses'=>'ExamController@postAddExaminee','as'=>'osce.admin.exam.postAddExaminee']);
		Route::get('exam/student-query',	['uses'=>'ExamController@getStudentQuery','as'=>'osce.admin.exam.getStudentQuery']);	//考生查询
		Route::get('exam/watch-status',	['uses'=>'ExamController@getWatchStatus','as'=>'osce.admin.exam.getWatchStatus']); //查询腕表是否绑定
		Route::get('exam/bound-watch',	['uses'=>'ExamController@getBoundWatch','as'=>'osce.admin.exam.getBoundWatch']);   //绑定腕表
		Route::get('exam/unwrap-watch',	['uses'=>'ExamController@getUnwrapWatch','as'=>'osce.admin.exam.getUnwrapWatch']); //解绑腕表
		Route::get('exam/student-details', 	['uses'=>'ExamController@getStudentDetails','as'=>'osce.admin.machine.getStudentDetails']);

		Route::get('exam/edit-exam', 	['uses'=>'ExamController@getEditExam','as'=>'osce.admin.exam.getEditExam']);	//考试基本信息编辑
		Route::post('exam/edit-exam', 	['uses'=>'ExamController@postEditExam','as'=>'osce.admin.exam.postEditExam']);
		Route::get('exam/examroom-assignment', 	['uses'=>'ExamController@getExamroomAssignment','as'=>'osce.admin.exam.getExamroomAssignment']); //考场安排
		Route::post('exam/examroom-assignment', 	['uses'=>'ExamController@postExamroomAssignmen','as'=>'osce.admin.exam.postExamroomAssignmen']); //考场安排
		Route::get('exam/room-list-data', ['uses'=>'ExamController@getRoomListData','as'=>'osce.admin.exam.getRoomListData']);			//获取考场列表
		Route::get('exam/station-data', ['uses'=>'ExamController@getStationData','as'=>'osce.admin.exam.getStationData']);				//获取考场对应的考站列表
		Route::get('exam/teacher-list-data', ['uses'=>'ExamController@getTeacherListData','as'=>'osce.admin.exam.getTeacherListData']);	//获取监考老师列表
		Route::get('exam/import-student', ['uses'=>'ExamController@getImportStudent','as'=>'osce.admin.exam.getImportStudent']);		//excel导入考生
		Route::post('exam/import-student', ['uses'=>'ExamController@postImportStudent','as'=>'osce.admin.exam.postImportStudent']);		//excel导入考生

		Route::post('exam/delete', 	['uses'=>'ExamController@postDelete','as'=>'osce.admin.exam.postDelete']);
		Route::get('exam/station-list', ['uses'=>'ExamController@getStationList','as'=>'osce.admin.exam.getStationList']);
		Route::get('exam/exam-list-data', ['uses'=>'ExamController@getExamListData','as'=>'osce.admin.exam.getExamListData']);
		Route::get('exam/exam-teacher-list', ['uses'=>'ExamController@getTeacherListData','as'=>'osce.admin.exam.getTeacherListData']);


		//智能排考
		Route::get('exam/intelligence-eaxm-plan', ['uses'=>'ExamController@getIntelligenceEaxmPlan','as'=>'osce.admin.exam.getIntelligenceEaxmPlan']);

		//sp
		Route::get('/spteacher/show', ['uses'=>'SpteacherController@getShow','as'=>'osce.admin.spteacher.getShow']);
		Route::get('/spteacher/invitation-index', ['uses'=>'SpteacherController@getInvitationIndex','as'=>'osce.admin.spteacher.getInvitationIndex']);
		Route::get('/spteacher/invitation-add', ['uses'=>'SpteacherController@getInvitationAdd','as'=>'osce.admin.spteacher.getInvitationAdd']);


		//通知
		Route::get('notice/msg', 	['uses'=>'NoticeController@getMsg','as'=>'osce.admin.notice.getMsg']);
		Route::get('notice/list', 	['uses'=>'NoticeController@getList','as'=>'osce.admin.notice.getList']);
		Route::get('notice/add-notice', 	['uses'=>'NoticeController@getAddNotice','as'=>'osce.admin.notice.getAddNotice']);
		Route::post('notice/add-notice', 	['uses'=>'NoticeController@postAddNotice','as'=>'osce.admin.notice.postAddNotice']);

		Route::get('notice/edit-notice', 	['uses'=>'NoticeController@getEditNotice','as'=>'osce.admin.notice.getEditNotice']);
		Route::get('notice/del-notice', 	['uses'=>'NoticeController@getDelNotice','as'=>'osce.admin.notice.getDelNotice']);
		Route::post('notice/edit-notice', 	['uses'=>'NoticeController@postEditNotice','as'=>'osce.admin.notice.postEditNotice']);

		//pad监考
		Route::get('invigilatepad/authentication', 	['uses'=>'InvigilatePadController@getAuthentication','as'=>'osce.admin.invigilatepad.getAuthentication']);
		Route::get('invigilatepad/exam-grade', 	['uses'=>'InvigilatePadController@getExamGrade','as'=>'osce.admin.invigilatepad.getExamGrade']);


		//测试
		Route::get('station/test', 	['uses'=>'StationController@getTest','as'=>'osce.admin.Station.getTest']);

	});

});


//微信端路由
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
	Route::group(['prefix'=>'wechat','namespace'=>'Wechat'],function(){

		//欢迎页
		Route::get('index/index',['uses'=>'IndexController@getIndex','as'=>'osce.wechat.index.getIndex']);
		//通知
		Route::get('notice/system-list',['uses'=>'NoticeController@getSystemList','as'=>'osce.wechat.notice.getSystemList']);
		//sp邀请
		Route::get('invitation/invitation-list',['uses'=>'InvitationController@getInvitationList','as'=>'osce.wechat.invitation.getInvitationList']);
		Route::get('invitation/invitation-respond',['uses'=>'InvitationController@getInvitationRespond','as'=>'osce.wechat.invitation.getInvitationRespond']);
		Route::get('invitation/msg',['uses'=>'InvitationController@getMsg','as'=>'osce.wechat.invitation.getMsg']);
		Route::get('invitation/list',['uses'=>'InvitationController@getList','as'=>'osce.wechat.invitation.getList']);

		Route::get('discussion/question-list',['uses'=>'DiscussionController@getQuestionList','as'=>'osce.wechat.getQuestionList']);
		Route::get('discussion/check-question',['uses'=>'DiscussionController@getCheckQuestion','as'=>'osce.wechat.getCheckQuestion']);
		Route::get('discussion/del-question',['uses'=>'DiscussionController@getDelQuestion','as'=>'osce.wechat.getDelQuestion']);


		Route::post('discussion/add-question',['uses'=>'DiscussionController@postAddQuestion','as'=>'osce.wechat.postAddQuestion']);
		Route::post('discussion/add-reply',['uses'=>'DiscussionController@postAddReply','as'=>'osce.wechat.postAddReply']);

		Route::get('train/train-list',['uses'=>'TrainController@getTrainList','as'=>'osce.wechat.getTrainList']);
		Route::get('train/edit-train',['uses'=>'TrainController@getEditTrain','as'=>'osce.wechat.getEditTrain']);
		Route::get('train/del-train',['uses'=>'TrainController@getDelTrain','as'=>'osce.wechat.getDelTrain']);
		Route::post('train/add-train',['uses'=>'TrainController@postAddTrain','as'=>'osce.wechat.postAddTrain']);
		Route::post('train/edit-train',['uses'=>'TrainController@postEditTrain','as'=>'osce.wechat.postEditTrain']);

		//考前培训
		Route::get('examtrain/exam-training-index',['uses'=>'ExamTrainController@getExamTrainingIndex','as'=>'osce.wechat.getExamTrainingIndex']);
		Route::post('examtrain/add-training',['uses'=>'ExamTrainController@postAddTraining','as'=>'osce.wechat.postAddTraining']);
		Route::get('examtrain/delete-training',['uses'=>'ExamTrainController@getDeleteTraining','as'=>'osce.wechat.getDeleteTraining']);
		Route::get('examtrain/see-training',['uses'=>'ExamTrainController@getSeeTraining','as'=>'osce.wechat.getSeeTraining']);

		//登陆
		Route::get('user/login',['uses'=>'UserController@getLogin','as'=>'osce.wechat.user.getLogin']);
		Route::post('user/login',['uses'=>'UserController@postLogin','as'=>'osce.wechat.user.postLogin']);

		//注册
		Route::get('user/register',['uses'=>'UserController@getRegister','as'=>'osce.wechat.user.getRegister']);
		Route::post('user/register',['uses'=>'UserController@postRegister','as'=>'osce.wechat.user.postRegister']);

		//发送重找账号
		Route::post('user/register',['uses'=>'UserController@postRegister','as'=>'osce.wechat.user.postRegister']);

	});

});


/**
 * WindowsAPP接口
 */
Route::group(['prefix' => "api/1.0/private/osce", 'namespace' => 'Modules\Osce\Http\Controllers','middleware' => ['oauth'],], function()
{
	Route::group(['prefix'=>'watch','namespace'=>'Api'],function(){

		Route::get('watch-status',	['uses'=>'IndexController@getWatchStatus']); //查询腕表是否绑定
		Route::get('bound-watch',	['uses'=>'IndexController@getBoundWatch']);   //绑定腕表
		Route::get('unwrap-watch',	['uses'=>'IndexController@getUnwrapWatch']); //解绑腕表
		Route::get('student-details', 	['uses'=>'IndexController@getStudentDetails']);

		Route::get('add',['uses'=>'IndexController@getAddWatch']);
		Route::get('update',['uses'=>'IndexController@getUpdateWatch']);
		Route::get('delete',['uses'=>'IndexController@getDeleteWatch']);
		Route::get('exam-list',['uses'=>'IndexController@getExamList']);


		Route::group(['prefix'=>'pad','namespace'=>'Api\Pad'],function(){
			Route::get('room-vcr',['uses'=>'PadController@getRoomVcr']);
			Route::get('vcr',['uses'=>'PadController@getVcr']);
			Route::get('student-vcr',['uses'=>'PadController@getStudentVcr']);
			Route::get('timing-vcr',['uses'=>'PadController@getTimingList']);
		});
	});



});



//TODO:测试用
Route::get('test/test', function() {
	$config = config('message');
	dd($config);
//	$config = include MESSAGE_CONFIG;
//	$formData['default'] = 'pm';
//	$config['default'] = 'env(\'MESSAGE_DRIVER\'， \'' . $formData['default'] . '\'' ."),\n";
//	echo $config['default'];
//	$num = strpos('sms_jia_pos','_');
//	dd(substr('sms_jia_pos',$num+1));
//	return view('osce::admin.test');
//	dd(filter_var('http://mb345.com/ws/BatchSend.aspx?' ));
});
Route::post('test/test',function(\Illuminate\Http\Request $request) {
	$test = $request->only('test');
	dd($test);
});

