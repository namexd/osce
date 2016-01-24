<?php
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers'], function () {
	Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
		Route::get('login/index', ['uses' => 'LoginController@getIndex', 'as' => 'osce.admin.getIndex']);
		Route::post('login/index', ['uses' => 'LoginController@postIndex', 'as' => 'osce.admin.postIndex']);
		//退出登录
		Route::get('user/logout',['uses'=>'UserController@getLogout','as'=>'osce.admin.user.getLogout']);
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
	Route::get('admin/index/dashboard', ['uses'=>'Admin\IndexController@dashboard','as'=>'osce.admin.index.dashboard']);
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

		//病例
		Route::controller('case','CaseController');
		Route::get('case/case-list', ['uses'=>'CaseController@getCaseList','as'=>'osce.admin.case.getCaseList']);  //病例的着陆页
		Route::get('case/edit-case', ['uses'=>'CaseController@getEditCase','as'=>'osce.admin.case.getEditCase']);  //病例的修改页
		Route::get('case/create-case', ['uses'=>'CaseController@getCreateCase','as'=>'osce.admin.case.getCreateCase']);  //病例的添加页
		Route::post('case/name-unique', 	['uses'=>'CaseController@postNameUnique','as'=>'osce.admin.case.postNameUnique']);	//判断名称是否存在
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


		//设置
		Route::get('config/index',  ['uses'=>'ConfigController@getIndex','as'=>'osce.admin.config.getIndex']);
		Route::post('config/store',  ['uses'=>'ConfigController@postStore','as'=>'osce.admin.config.postStore']);
		Route::get('config/area',  ['uses'=>'ConfigController@getArea','as'=>'osce.admin.config.getArea']);
		Route::get('config/area-store',  ['uses'=>'ConfigController@getAreaStore','as'=>'osce.admin.config.getAreaStore']);
		Route::post('config/area-store', ['uses'=>'ConfigController@postAreaStore','as'=>'osce.admin.config.postAreaStore']);
		Route::post('config/del-area',	 ['uses'=>'ConfigController@postDelArea','as'=>'osce.admin.config.postDelArea']);
		Route::post('config/name-unique', 	['uses'=>'ConfigController@postNameUnique','as'=>'osce.admin.config.postNameUnique']);	//判断名称是否存在


		Route::post('invigilator/select-teacher',	['uses'=>'InvigilatorController@postSelectTeacher', 'as'=>'osce.admin.invigilator.postSelectTeacher']);


		Route::post('invigilator/del-invitation', 	['uses'=>'InvigilatorController@postDelInvitation','as'=>'osce.admin.invigilator.postDelInvitation']);
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
		Route::post('machine/machine-delete', 	['uses'=>'MachineController@postMachineDelete','as'=>'osce.admin.machine.postMachineDelete']);
		Route::post('machine/name-unique',['uses'=>'MachineController@postNameUnique','as'=>'osce.admin.machine.postNameUnique']);	//判断名称是否存在


		//考核点
		Route::get('topic/list', 	['uses'=>'TopicController@getList','as'=>'osce.admin.topic.getList']);
		Route::get('topic/add-topic', 	['uses'=>'TopicController@getAddTopic','as'=>'osce.admin.topic.getAddTopic']);
		Route::post('topic/add-topic', 	['uses'=>'TopicController@postAddTopic','as'=>'osce.admin.topic.postAddTopic']);
		Route::get('topic/edit-topic', 	['uses'=>'TopicController@getEditTopic','as'=>'osce.admin.topic.getEditTopic']);
		Route::post('topic/edit-topic', 	['uses'=>'TopicController@postEditTopic','as'=>'osce.admin.topic.postEditTopic']);
		Route::post('topic/import-excel', 	['uses'=>'TopicController@postImportExcel','as'=>'osce.admin.topic.postImportExcel']);
		Route::get('topic/toppic-tpl', 	['uses'=>'TopicController@getToppicTpl','as'=>'osce.admin.topic.getToppicTpl']);
		Route::get('topic/del-topic', 	['uses'=>'TopicController@getDelTopic','as'=>'osce.admin.topic.getDelTopic']);
		Route::post('topic/name-unique',['uses'=>'TopicController@postNameUnique','as'=>'osce.admin.topic.postNameUnique']);	//判断名称是否存在

		//病例
		Route::post('case/delete', 	['uses'=>'CaseController@postDelete','as'=>'osce.admin.case.postDelete']);



		//考站
		Route::get('station/station-list', 	['uses'=>'StationController@getStationList','as'=>'osce.admin.Station.getStationList']);
		Route::get('station/edit-station', 	['uses'=>'StationController@getEditStation','as'=>'osce.admin.Station.getEditStation']);
		Route::get('station/add-station', 	['uses'=>'StationController@getAddStation','as'=>'osce.admin.Station.getAddStation']);
		Route::post('station/add-station', ['uses'=>'StationController@postAddStation','as'=>'osce.admin.Station.postAddStation']);
		Route::post('station/delete-station', ['uses'=>'StationController@postDelete','as'=>'osce.admin.Station.postDelete']);
		Route::post('station/edit-station', 	['uses'=>'StationController@postEditStation','as'=>'osce.admin.Station.postEditStation']);
		Route::post('station/name-unique', 	['uses'=>'StationController@postNameUnique','as'=>'osce.admin.station.postNameUnique']);


		//场所分类
		Route::get('room-cate/room-cate-list',['uses'=>'RoomCateController@getRoomCateList','as'=>'osce.admin.room-cate.getRoomCateList']);
		Route::get('room-cate/edit-room-cate',['uses'=>'RoomCateController@getEditRoomCate','as'=>'osce.admin.room-cate.getEditRoomCate']);
		Route::post('room-cate/delete',['uses'=>'RoomCateController@postDelete','as'=>'osce.admin.room-cate.postDelete']);   //场所的删除

		//考场
		Route::post('room/delete',['uses'=>'RoomController@postDelete','as'=>'osce.admin.room.postDelete']);
		Route::get('room/room-list',['uses'=>'RoomController@getRoomList','as'=>'osce.admin.room.getRoomList']);
		Route::post('room/name-unique',['uses'=>'RoomController@postNameUnique','as'=>'osce.admin.room.postNameUnique']);	//判断名称是否存在

		//用户管理
		Route::get('user/staff-list', 	['uses'=>'UserController@getStaffList','as'=>'osce.admin.user.getStaffList']);
		Route::get('user/edit-staff', 	['uses'=>'UserController@getEditStaff','as'=>'osce.admin.user.getEditStaff']);
		Route::get('user/add-user', 	['uses'=>'UserController@getAddUser','as'=>'osce.admin.user.getAddUser']);
		Route::post('user/del-user', 	['uses'=>'UserController@postDelUser','as'=>'osce.admin.user.postDelUser']);	//删除用户
		Route::post('user/add-user', 	['uses'=>'UserController@postAddUser','as'=>'osce.admin.user.postAddUser']);
		Route::post('user/edit-user', 	['uses'=>'UserController@postEditUser','as'=>'osce.admin.user.postEditUser']);

		//考试
		Route::get('exam/exam-list', 	['uses'=>'ExamController@getExamList','as'=>'osce.admin.exam.getExamList']);
		Route::get('exam/delete', 	['uses'=>'ExamController@postDelete','as'=>'osce.admin.exam.postDelete']);
		Route::get('exam/choose-exam-arrange', 	['uses'=>'ExamController@getChooseExamArrange','as'=>'osce.admin.exam.getChooseExamArrange']);  //判定应该载入哪个安排页面
		Route::post('exam/station-assignment', 	['uses'=>'ExamController@postStationAssignment','as'=>'osce.admin.exam.postStationAssignment']);

		Route::get('exam/download-student-improt-tpl', 	['uses'=>'ExamController@getdownloadStudentImprotTpl','as'=>'osce.admin.exam.getdownloadStudentImprotTpl']);

		Route::get('exam/ajax-station-row', ['uses'=>'ExamController@getAjaxStationRow','as'=>'osce.admin.exam.getAjaxStationRow']);  //以json返回考站信息
		Route::get('exam/ajax-station', ['uses'=>'ExamController@getAjaxStation','as'=>'osce.admin.exam.getAjaxStation']);  //以json返回考站信息
		Route::get('exam/add-exam', 	['uses'=>'ExamController@getAddExam','as'=>'osce.admin.exam.getAddExam']);		//新增考试
		Route::post('exam/add-exam', 	['uses'=>'ExamController@postAddExam','as'=>'osce.admin.exam.postAddExam']);
		Route::get('exam/examinee-manage', 	['uses'=>'ExamController@getExamineeManage','as'=>'osce.admin.exam.getExamineeManage']);  //考生管理
		Route::post('exam/del-student', 		['uses'=>'ExamController@postDelStudent','as'=>'osce.admin.exam.postDelStudent']);		//删除考生
		Route::get('exam/add-examinee', 	['uses'=>'ExamController@getAddExaminee','as'=>'osce.admin.exam.getAddExaminee']);		//添加考生
		Route::get('exam/edit-examinee', 	['uses'=>'ExamController@getEidtExaminee','as'=>'osce.admin.exam.getEidtExaminee']);		//添加考生
		Route::post('exam/add-examinee', 	['uses'=>'ExamController@postAddExaminee','as'=>'osce.admin.exam.postAddExaminee']);
		Route::post('exam/edit-examinee', 	['uses'=>'ExamController@postEditExaminee','as'=>'osce.admin.exam.postEditExaminee']);
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
		Route::post('exam/import-student/{id?}', ['uses'=>'ExamController@postImportStudent','as'=>'osce.admin.exam.postImportStudent']);		//excel导入考生

		Route::post('exam/delete', 	['uses'=>'ExamController@postDelete','as'=>'osce.admin.exam.postDelete']);
		Route::get('exam/station-list', ['uses'=>'ExamController@getStationList','as'=>'osce.admin.exam.getStationList']);
		Route::get('exam/exam-list-data', ['uses'=>'ExamController@getExamListData','as'=>'osce.admin.exam.getExamListData']);
		Route::get('exam/exam-teacher-list', ['uses'=>'ExamController@getTeacherListData','as'=>'osce.admin.exam.getTeacherListData']);


		//智能排考
		Route::get('exam/intelligence-eaxm-plan', ['uses'=>'ExamController@getIntelligenceEaxmPlan','as'=>'osce.admin.exam.getIntelligenceEaxmPlan']);
		Route::get('exam/intelligence', ['uses'=>'ExamController@getIntelligence','as'=>'osce.admin.exam.getIntelligence']);
		Route::post('exam/intelligence', ['uses'=>'ExamController@postIntelligence','as'=>'osce.admin.exam.postIntelligence']);
		Route::post('exam/save-exam-plan', ['uses'=>'ExamController@postSaveExamPlan','as'=>'osce.admin.exam.postSaveExamPlan']);
		Route::get('exam/change-student', ['uses'=>'ExamController@getChangeStudent','as'=>'osce.admin.exam.getChangeStudent']);

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


		//候考
		Route::get('oscetv/wait-detail',['uses'=>'OsceTvController@getWaitDetail','as'=>'osce.admin.getWaitDetail']);

		//测试
		Route::get('station/test', 	['uses'=>'StationController@getTest','as'=>'osce.admin.Station.getTest']);
        //考前培训
		Route::get('train/train-list',['uses'=>'TrainController@getTrainList','as'=>'osce.admin.getTrainList']);
		Route::get('train/train-detail',['uses'=>'TrainController@getTrainDetail','as'=>'osce.admin.getTrainDetail']);
//		Route::get('train/edit-train',['uses'=>'TrainController@getEditTrain','as'=>'osce.wechat.getEditTrain']);
//		Route::get('train/del-train',['uses'=>'TrainController@getDelTrain','as'=>'osce.wechat.getDelTrain']);
//		Route::post('train/add-train',['uses'=>'TrainController@postAddTrain','as'=>'osce.wechat.postAddTrain']);
//		Route::post('train/edit-train',['uses'=>'TrainController@postEditTrain','as'=>'osce.wechat.postEditTrain']);

		Route::get('train/edit-train',['uses'=>'TrainController@getEditTrain','as'=>'osce.admin.getEditTrain']);
		Route::get('train/del-train',['uses'=>'TrainController@getDelTrain','as'=>'osce.admin.getDelTrain']);
		Route::get('train/add-train',['uses'=>'TrainController@getAddTrain','as'=>'osce.admin.getAddTrain']);
		Route::post('train/add-train',['uses'=>'TrainController@postAddTrain','as'=>'osce.admin.postAddTrain']);
		Route::post('train/upload-file',['uses'=>'TrainController@postUploadFile','as'=>'osce.admin.postUploadFile']);
		Route::post('train/edit-train',['uses'=>'TrainController@postEditTrain','as'=>'osce.admin.postEditTrain']);
		Route::post('train/upload-file',['uses'=>'TrainController@postUploadFile','as'=>'osce.admin.postUploadFile']);
		Route::get('train/download-document',['uses'=>'TrainController@getDownloadDocument','as'=>'osce.admin.getDownloadDocument']);
	});

//	 //Pad端
//	Route::group(['prefix'=>'pad','namespace'=>'Api\Pad'],function(){
//		Route::get('room-vcr',['uses'=>'PadController@getRoomVcr','as'=>'osce.pad.getRoomVcr']);
//		Route::get('vcr',['uses'=>'PadController@getVcr']);
//
//		Route::get('student-vcr',['uses'=>'PadController@getStudentVcr']);
//		Route::get('timing-vcr',['uses'=>'PadController@getTimingList']);
//
//		Route::get('wait-student',['uses'=>'PadController@getWaitStudent']);
//
//		Route::get('exam-room',['uses'=>'PadController@getExamRoom']);
//		Route::get('wait-room',['uses'=>'PadController@getWaitRoom']);
//
//		Route::get('examinee',['uses'=>'DrawlotsController@getExaminee','as'=>'osce.pad.getExaminee']);  //pad端通过教师查询考室id
//		Route::get('station',['uses'=>'DrawlotsController@getStation','as'=>'osce.pad.getStation']);
//	});


});


//微信端路由
Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
	Route::group(['prefix'=>'wechat','namespace'=>'Wechat'],function(){

		//欢迎页
		Route::get('index/index',['uses'=>'IndexController@getIndex','as'=>'osce.wechat.index.getIndex']);
		//通知
		Route::get('notice/system-list',['uses'=>'NoticeController@getSystemList','as'=>'osce.wechat.notice.getSystemList']);
		Route::get('notice/view',['uses'=>'NoticeController@getView','as'=>'osce.wechat.notice.getView']);
		Route::get('notice-list/system-list',['uses'=>'NoticeListController@getSystemList','as'=>'osce.wechat.notice-list.getSystemList']);
		Route::get('notice-list/view',['uses'=>'NoticeListController@getView','as'=>'osce.wechat.notice-list.getView']);


		//sp邀请
		Route::get('invitation/invitation-list',['uses'=>'InvitationController@getInvitationList','as'=>'osce.wechat.invitation.getInvitationList']);
		Route::get('invitation/invitation-respond',['uses'=>'InvitationController@getInvitationRespond','as'=>'osce.wechat.invitation.getInvitationRespond']);
		Route::get('invitation/msg',['uses'=>'InvitationController@getMsg','as'=>'osce.wechat.invitation.getMsg']);
		Route::get('invitation/list',['uses'=>'InvitationController@getList','as'=>'osce.wechat.invitation.getList']);

		//讨论区
		Route::get('discussion/question-list',['uses'=>'DiscussionController@getQuestionList','as'=>'osce.wechat.getQuestionList']);
		Route::get('discussion/check-question',['uses'=>'DiscussionController@getCheckQuestion','as'=>'osce.wechat.getCheckQuestion']);
		Route::get('discussion/check-question-json',['uses'=>'DiscussionController@getCheckQuestions','as'=>'osce.wechat.getCheckQuestions']);
		Route::get('discussion/del-question',['uses'=>'DiscussionController@getDelQuestion','as'=>'osce.wechat.getDelQuestion']);
        Route::get('discussion/question-lists',['uses'=>'DiscussionController@getDiscussionLists','as'=>'osce.wechat.getDiscussionLists']);
		

		Route::post('discussion/add-question',['uses'=>'DiscussionController@postAddQuestion','as'=>'osce.wechat.postAddQuestion']);
		Route::get('discussion/add-question',['uses'=>'DiscussionController@getAddQuestion','as'=>'osce.wechat.getAddQuestion']);
		Route::get('discussion/add-reply',['uses'=>'DiscussionController@getAddReply','as'=>'osce.wechat.getAddReply']);
		Route::post('discussion/add-reply',['uses'=>'DiscussionController@postAddReply','as'=>'osce.wechat.postAddReply']);
		Route::get('discussion/edit-question',['uses'=>'DiscussionController@getEditQuestion','as'=>'osce.wechat.getEditQuestion']);
		Route::post('discussion/edit-question',['uses'=>'DiscussionController@postEditQuestion','as'=>'osce.wechat.postEditQuestion']);

		//考前培训
		Route::get('train/train-list',['uses'=>'TrainController@getTrainList','as'=>'osce.wechat.getTrainList']);
		Route::get('train/train-detail',['uses'=>'TrainController@getTrainDetail','as'=>'osce.wechat.getTrainDetail']);
		Route::get('train/train-lists',['uses'=>'TrainController@getTrainlists','as'=>'osce.wechat.getTrainlists']);


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

Route::group(['prefix' => "osce", 'namespace' => 'Modules\Osce\Http\Controllers', 'middleware' => []], function () {
	Route::group(['prefix'=>'api','namespace'=>'Api'],function(){
		Route::post('communal-api/attch-upload',['uses'=>'CommunalApiController@postAttchUpload','as'=>'osce.api.communal-api.postAttchUpload']);
		Route::get('communal-api/editor-upload',['uses'=>'CommunalApiController@getEditorUpload','as'=>'osce.api.communal-api.getEditorUpload']);
		Route::post('communal-api/editor-upload',['uses'=>'CommunalApiController@postEditorUpload','as'=>'osce.api.communal-api.postEditorUpload']);

		//学生腕表
		Route::get('student-watch/wait-exam-list',['uses'=>'StudentWatchController@getWaitExamList']);
		Route::get('student-watch/student-exam-reminder',['uses'=>'StudentWatchController@getStudentExamReminder']);

		//pad监考
		Route::get('invigilatepad/authentication', 	['uses'=>'InvigilatePadController@getAuthentication','as'=>'osce.api.invigilatepad.getAuthentication']);
		Route::get('invigilatepad/exam-grade', 	['uses'=>'InvigilatePadController@getExamGrade','as'=>'osce.api.invigilatepad.getExamGrade']);
		Route::post('invigilatepad/save-exam-Result', 	['uses'=>'InvigilatePadController@postSaveExamResult','as'=>'osce.api.invigilatepad.postSaveExamResult']);
		Route::post('invigilatepad/save-exam-evaluate', 	['uses'=>'InvigilatePadController@postSaveExamEvaluate','as'=>'osce.api.invigilatepad.postSaveExamEvaluate']);
		Route::get('invigilatepad/wait_exam', 	['uses'=>'InvigilatePadController@getWaitExam','as'=>'osce.api.invigilatepad.getWaitExam']);
		Route::get('invigilatepad/wait_exam_list', 	['uses'=>'InvigilatePadController@getWaitExamList','as'=>'osce.api.invigilatepad.getWaitExamList']);
		Route::get('invigilatepad/start-exam', 	['uses'=>'InvigilatePadController@getStartExam','as'=>'osce.api.invigilatepad.getStartExam']);
		Route::get('invigilatepad/end-exam', 	['uses'=>'InvigilatePadController@getEndExam','as'=>'osce.api.invigilatepad.getEndExam']);

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
		Route::get('student-list', 	['uses'=>'IndexController@getStudentList']);
		Route::get('skip-last', 	['uses'=>'IndexController@getSkipLast']);

		Route::get('add',['uses'=>'IndexController@getAddWatch']);
		Route::get('update',['uses'=>'IndexController@getUpdateWatch']);
		Route::get('delete',['uses'=>'IndexController@getDeleteWatch']);
		Route::get('exam-list',['uses'=>'IndexController@getExamList']);

		Route::get('list',['uses'=>'IndexController@getWatchList']);
		Route::get('watch-detail',['uses'=>'IndexController@getWatchDetail']);

		//学生腕表

		Route::get('wait_exam',['uses'=>'StudentWatchController@getWaitExam']);



		Route::group(['prefix'=>'pad','namespace'=>'Api\Pad'],function(){
			Route::get('room-vcr',['uses'=>'PadController@getRoomVcr']);
			Route::get('vcr',['uses'=>'PadController@getVcr']);

			Route::get('student-vcr',['uses'=>'PadController@getStudentVcr']);
			Route::get('timing-vcr',['uses'=>'PadController@getTimingList']);

			Route::get('wait-student',['uses'=>'PadController@getWaitStudent']);//大屏幕候考接口

			Route::get('exam-room',['uses'=>'PadController@getExamRoom']);
			Route::get('wait-room',['uses'=>'PadController@getWaitRoom']);

		});
	});



});



//TODO:测试用
Route::get('test/test', function() {
	$config = config('osce.room_cate');
	dd($config);

});
Route::post('test/test',function(\Illuminate\Http\Request $request) {
	$test = $request->only('test');
	dd($test);
});

