<?php



Route::group(['prefix' => 'msc', 'namespace' => 'Modules\Msc\Http\Controllers'], function()
{
	Route::get('/', 'MscController@index');

});

Route::group(['prefix' => "msc",'namespace' => 'Modules\Msc\Http\Controllers','middleware' => []], function()
{

	//http://www.mis.hx/msc/admin/resources-manager/user-register
	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){

		Route::controller('resources-manager', 'ResourcesManagerController');//TODO： 2015-12-11 罗海华 解决 路由被拦截的问题
		Route::get('resources-manager/resources-list', ['uses'=>'ResourcesManagerController@getWaitExamineList','as'=>'msc.admin.resourcesManager.getWaitExamineList']);
		Route::get('resources-manager/rejected-resources', ['uses'=>'ResourcesManagerController@getRejectedResources','as'=>'msc.admin.resourcesManager.getRejectedResources']);
		Route::post('resources-manager/rejected-resources-all', ['uses'=>'ResourcesManagerController@postRejectedResourcesAll','as'=>'msc.admin.resourcesManager.postRejectedResourcesAll']);
		Route::get('resources-manager/resources', ['uses'=>'ResourcesManagerController@getResources','as'=>'msc.admin.resourcesManager.getResources']);
		Route::post('resources-manager/rejected-resources', ['uses'=>'ResourcesManagerController@postRejectedResources','as'=>'msc.admin.resourcesManager.postRejectedResources']);
		Route::post('resources-manager/edit-resources', ['uses'=>'ResourcesManagerController@postEditResources','as'=>'msc.admin.resourcesManager.postEditResources']);
		Route::get('resources-manager/resources-list', ['uses'=>'ResourcesManagerController@getResourcesList','as'=>'msc.admin.resourcesManager.getResourcesList']);
		Route::get('resources-manager/ajax-resources-tools-cate', ['uses'=>'ResourcesManagerController@getAjaxResourcesToolsCate','as'=>'msc.admin.resourcesManager.getAjaxResourcesToolsCate']);
		Route::get('resources-manager/add-resources', ['uses'=>'ResourcesManagerController@getAddResources','as'=>'msc.admin.resourcesManager.getAddResources']);
		Route::get('resources-manager/resources-cate-list', ['uses'=>'ResourcesManagerController@getResourcesCateList','as'=>'msc.admin.resourcesManager.getResourcesCateList']);

		Route::get('resources-manager/classroom-list', ['uses'=>'ResourcesManagerController@getClassroomList','as'=>'msc.resourcesManager.classroomList']);
		Route::get('resources-manager/record-info', ['uses'=>'ResourcesManagerController@getRecordInfo','as'=>'msc.admin.resourcesManager.getRecordInfo']);
		Route::get('resources-manager/borrow-record-list', ['uses'=>'ResourcesManagerController@getBorrowRecordList','as'=>'msc.admin.resourcesManager.getBorrowRecordList']);
		Route::get('resources-manager/statistics', ['uses'=>'ResourcesManagerController@getStatistics','as'=>'msc.admin.resourcesManager.getStatistics']);
		Route::post('resources-manager/examine-borrow-apply', ['uses'=>'ResourcesManagerController@postExamineBorrowingApply','as'=>'msc.admin.resourcesManager.postExamineBorrowingApply']);
		Route::get('resources-manager/wait-examine-list', ['uses'=>'ResourcesManagerController@getWaitExamineList','as'=>'msc.admin.resourcesManager.getWaitExamineList']);
		Route::post('resources-manager/examine-borrowing-apply', ['uses'=>'ResourcesManagerController@postExamineBorrowingApply','as'=>'msc.admin.resourcesManager.postExamineBorrowingApply']);
		Route::get('resources-manager/borrowed-list', ['uses'=>'ResourcesManagerController@getBorrowedList','as'=>'msc.admin.resourcesManager.getBorrowedList']);
		Route::get('resources-manager/tip-back', ['uses'=>'ResourcesManagerController@getTipBack','as'=>'msc.admin.resourcesManager.getTipBack']);

		//Route::controller('examine', 'ExamineController');
		Route::controller('courses', 'CoursesController');

		Route::get('courses/normal-courses-plan', ['uses'=>'CoursesController@getNormalCoursesPlan','as'=>'msc.courses.NormalCoursesPlan']);

		//课程编辑
		Route::get('courses/courses-edit',['uses'=>'CoursesController@getCoursesEdit','as'=>'msc.courses.coursesEdit']);
		//课程详情
		Route::get('courses/courses',['uses'=>'CoursesController@getCourses','as'=>'msc.courses.courses']);

		//开放实验室审核
		Route::get('lab/open-lab-apply-list',['uses'=>'LabController@getOpenLabApplyList','as'=>'msc.admin.lab.openLabApplyList']);
		//Route::get('lab/open-lab-apply-list',['uses'=>'LabController@getOpenLabApplyList','as'=>'msc.lab.openLabApplyList']);
		//开放实验室已审核
		Route::get('lab/open-lab-apply-examined-list',['uses'=>'LabController@getOpenLabApplyExaminedList','as'=>'msc.admin.lab.openLabApplyExaminedList']);

		Route::get('courses/provisional-courses-plan', ['uses'=>'CoursesController@getProvisionalCoursesPlan','as'=>'msc.courses.ProvisionalCoursesPlan']);
		Route::get('courses/best-time', ['uses'=>'CoursesController@getBestTime','as'=>'msc.courses.BestTime']);
		Route::get('courses/classroom-time', ['uses'=>'CoursesController@getClassroomTime','as'=>'msc.courses.ClassroomTime']);
		Route::get('courses/video-check', ['uses'=>'CoursesController@getClassroomTime','as'=>'msc.courses.ClassroomTime']);

		Route::get('courses/download-courses-list-tpl',['uses'=>'CoursesController@getDownloadCoursesListTpl','as'=>'msc.admin.courses.getDownloadCoursesListTpl']);
		Route::get('courses/download-courses-plan-tpl',['uses'=>'CoursesController@getDownloadCoursesPlanTpl','as'=>'msc.admin.courses.getDownloadCoursesPlanTpl']);
		Route::get('courses/video-check', ['uses'=>'CoursesController@getVideoCheck','as'=>'msc.courses.getVideoCheck']);


		Route::controller('verify', 'VerifyController');
		Route::get('verify/student/{status?}', ['uses'=>'VerifyController@getStudent','as'=>'msc.verify.student']);
		Route::get('verify/teacher/{status?}', ['uses'=>'VerifyController@getTeacher','as'=>'msc.verify.teacher']);

		Route::controller('training', 'TrainingController');
		Route::get('training/ajax-checkname/{name}', ['uses'=>'TrainingController@getUniqueName','as'=>'msc.training.ajaxUniqueName']);
		Route::get('training/add-training-group/{id}', ['uses'=>'TrainingController@getAddTrainingGroup','as'=>'msc.training.addTrainingGroup']);
		Route::get('training/add-training-plan/{id}', ['uses'=>'TrainingController@getAddTrainingPlan','as'=>'msc.training.addTrainingPlan']);
		Route::get('training/add-training-preview/{id}', ['uses'=>'TrainingController@getAddTrainingPreview','as'=>'msc.training.addTrainingPreview']);
		Route::get('training/edit-training-group/{id}', ['uses'=>'TrainingController@getEditTrainingGroup','as'=>'msc.training.editTrainingGroup']);
		Route::get('training/ajax-course-classroom/{id}', ['uses'=>'TrainingController@getClassrooms','as'=>'msc.training.ajaxGetClassrooms']);
		Route::get('training/ajax-classroom-freetime/{courseId}/{classroomId}', ['uses'=>'TrainingController@getFreetime','as'=>'msc.training.ajaxGetFreetime']);
		Route::get('training/ajax-conflictcourses/{type}', ['uses'=>'TrainingController@getConflictCourses','as'=>'msc.training.ajaxGetConflictCourses']);

		Route::controller('lab', 'LabController');
		Route::get('lab/openlab-history-item/{id}', ['uses'=>'LabController@getOpenlabHistoryItem','as'=>'msc.lab.getOpenlabHistoryItem']);

		Route::get('lab/lab-emptytime/{id}/{time}',['uses'=>'LabController@getLabEmptytime','as'=>'msc.lab.getLabEmptytime']);
		Route::post('lab/agree-emergency-apply',['uses'=>'LabController@postAgreeEmergencyApply','as'=>'msc.admin.lab.postAgreeEmergencyApply']);
		Route::get('lab/agree-emergency-apply',['uses'=>'LabController@getAgreeEmergencyApply','as'=>'msc.admin.lab.getAgreeEmergencyApply']);
		Route::post('lab/refund-emergency-apply',['uses'=>'LabController@postRefundEmergencyApply','as'=>'msc.admin.lab.postRefundEmergencyApply']);
		Route::get('lab/urgent-apply-list',['uses'=>'LabController@getUrgentApplyList','as'=>'msc.admin.lab.getUrgentApplyList']);//TDDO ：luohaihua 以当前别名为准
		Route::get('lab/openlab-history-list',['uses'=>'LabController@getOpenlabHistoryList','as'=>'msc.admin.lab.openlabHistoryList']);
		Route::get('lab/open-lab-apply-list',['uses'=>'LabController@getOpenLabApplyList','as'=>'msc.admin.lab.openLabApplyList']);


		Route::controller('lab-tools', 'LabToolsController');
		//开放设备待审核列表
		Route::get('lab-tools/open-lab-tools-apply-list', ['uses'=>'LabToolsController@getOpenLabToolsApplyList','as'=>'msc.admin.lab-tools.getOpenLabToolsApplyList']);
		//Route::get('lab-tools/open-lab-tools-apply-list', ['uses'=>'LabToolsController@getOpenLabToolsApplyList','as'=>'msc.lab-tools.openLabToolsApplyList']);//路由重复，请使用上面的那个todo:luohaihua
		//开放设备审核状态改变
		Route::get('lab-tools/change-open-lab-tools-apply-status', ['uses'=>'LabToolsController@postChangeOpenLabToolsApplyStatus','as'=>'msc.lab-tools.changeOpenLabToolsApplyStatus']);
		//开放设备紧急通知
		Route::get('lab-tools/open-lab-tools-urgent-notice', ['uses'=>'LabToolsController@postOpenLabToolsUrgentNotice','as'=>'msc.lab-tools.OpenLabToolsUrgentNotice']);
		//开放设备已预约
		Route::get('lab-tools/open-lab-tools-examined-list', ['uses'=>'LabToolsController@getOpenLabToolsExaminedList','as'=>'msc.admin.lab-tools.openLabToolsExaminedList']);
		Route::get('lab-tools/history-list', ['uses'=>'LabToolsController@getHistoryList','as'=>'admin.lab-tools.historyList']);
		Route::get('lab-tools/history-statistics-data', ['uses'=>'LabToolsController@getHistoryStatisticsData','as'=>'msc.lab-tools.getHistoryStatisticsData']);
		Route::get('lab-tools/history-statistics-excl', ['uses'=>'LabToolsController@getHistoryStatisticsExcl','as'=>'msc.lab-tools.getHistoryStatisticsExcl']);
		Route::get('lab-tools/open-lab-tools-use-history',['uses'=>'LabToolsController@getOpenLabToolsUseHistory','as'=>'msc.admin.lab-tools.getOpenLabToolsUseHistory']);
		Route::get('lab-tools/open-lab-tools-use-history-view/{$id}',['uses'=>'LabToolsController@getOpenLabToolsUseHistoryView','as'=>'msc.admin.lab-tools.getOpenLabToolsUseHistoryView']);
		//开放设备审核状态改变
		Route::get('lab-tools/change-open-lab-tools-apply-status', ['uses'=>'LabToolsController@postChangeOpenLabToolsApplyStatus','as'=>'msc.admin.lab-tools.postChangeOpenLabToolsApplyStatus']);
		Route::get('lab-tools/open-lab-tools-use-history', ['uses'=>'LabToolsController@getOpenLabToolsUseHistory','as'=>'msc.admin.lab-tools.getOpenLabToolsUseHistory']);

		Route::controller('user', 'UserController');
		Route::get('user/student-list', ['uses'=>'UserController@getStudentList','as'=>'msc.admin.user.StudentList']);
		Route::get('user/student-item/{id}', ['uses'=>'UserController@getStudentItem','as'=>'msc.admin.user.StudentItem']);
		Route::get('user/teacher-list', ['uses'=>'UserController@getTeacherList','as'=>'msc.admin.user.TeacherList']);
		Route::get('user/teacher-item/{id}', ['uses'=>'UserController@getTeacherItem','as'=>'msc.admin.user.TeacherItem']);


		Route::get('user/student-edit/{id}', ['uses'=>'UserController@getStudentEdit','as'=>'msc.admin.user.StudentEdit']);
		Route::get('user/student-trashed/{id}', ['uses'=>'UserController@getStudentTrashed','as'=>'msc.admin.user.StudentTrashed']);
		Route::get('user/student-status/{id}', ['uses'=>'UserController@getStudentStatus','as'=>'msc.admin.user.StudentStatus']);

		Route::post('user/student-add', ['uses'=>'UserController@postStudentAdd','as'=>'msc.admin.user.StudentAdd']);
		Route::post('user/student-save', ['uses'=>'UserController@postStudentSave','as'=>'msc.admin.user.StudentSaveEdit']);


	});

	Route::group(['prefix'=>'wechat','namespace'=>'WeChat','middleware' => ['wechatauth']],function(){

		Route::controller('user', 'UserController');
		Route::controller('wechat', 'WeChatController');
		Route::controller('personal-center', 'PersonalCenterController');
		Route::get('personal-center/cancel-open-device-apply',['uses'=>'PersonalCenterController@getCancelOpenDeviceApply','as'=>'msc.personalCenter.cancelOpenDeviceApply']);
		//开放设备当前预约
		Route::get('personal-center/my-apply',['uses'=>'PersonalCenterController@getMyApply','as'=>'msc.personalCenter.myApply']);
		//开放设备使用历史
		Route::get('personal-center/user-open-device-histroy-data',['uses'=>'PersonalCenterController@getUserOpenDeviceHistroyData','as'=>'msc.personalCenter.userOpenDeviceHistroyData']);
		//开放设备取消预约
		Route::get('personal-center/cancel-open-device-apply',['uses'=>'PersonalCenterController@getCancelOpenDeviceApply','as'=>'msc.personalCenter.cancelOpenDeviceApply']);
		Route::get('personal-center/info-manage',['uses'=>'PersonalCenterController@getInfoManage','as'=>'msc.personalCenter.infoManage']);

		Route::controller('resource', 'ResourceController');
		Route::controller('resources-manager', 'ResourcesManagerController');
		Route::controller('course-order', 'CourseOrderController');
		Route::controller('open-laboratory', 'OpenLaboratoryController');
		Route::get('open-laboratory/emergency-manage',['uses'=>'OpenLaboratoryController@getEmergencyManage','as'=>'msc.wechat.openLaboratory.emergencyManage']);
		Route::controller('lab', 'LabController');
		Route::get('lab/refund-emergency-apply', ['uses'=>'LabController@getRefundEmergencyApply','as'=>'mswechat.lab.getRefundEmergencyApply']);

		Route::get('lab/openlab-history-item/{id}', ['uses'=>'LabController@getOpenlabHistoryItem','as'=>'wechat.lab.getOpenlabHistoryItem']);
		Route::get('lab/open-lab-apply-list', ['uses'=>'LabController@getOpenLabApplyList','as'=>'wechat.lab.openLabApplyList']);
		Route::post('lab/change-open-lab-apply-status', ['uses'=>'LabController@postChangeOpenLabApplyStatus','as'=>'msc.wechat.lab.changeOpenLabApplyStatus']);
		Route::get('lab/agree-emergency-apply', ['uses'=>'LabController@getAgreeEmergencyApply','as'=>'msc.wechat.lab.agreeEmergencyApply']);

		Route::controller('open-device', 'OpenDeviceController');

		Route::get('open-device/open-tools-order-index', ['uses'=>'OpenDeviceController@getOpenToolsOrderIndex','as'=>'wechat.lab-tools.getOpenToolsOrderIndex']);
		Route::get('open-device/open-tools-time-sec/{id}/{date}', ['uses'=>'OpenDeviceController@getOpenToolsTimeSec','as'=>'wechat.lab-tools.getOpenToolsTimeSec']);
		Route::get('open-device/open-tools-apply/{id}/{date}/{timeSec}', ['uses'=>'OpenDeviceController@getOpenToolsApply','as'=>'wechat.lab-tools.getOpenToolsApply']);
		Route::get('open-device/open-tools-delay/{deviceId}/{planId}', ['uses'=>'OpenDeviceController@getOpenToolsDelay','as'=>'wechat.lab-tools.getOpenToolsDelay']);
		Route::get('open-device/open-tools-order-search', ['uses'=>'OpenDeviceController@getOpenToolsOrderSearch','as'=>'wechat.open-device.openToolsOrderSearch']);
		Route::get('open-device/open-tools-used-info/{code}', ['uses'=>'OpenDeviceController@getOpenToolsUsedInfo','as'=>'wechat.open-device.getOpenToolsUsedInfo']);

		Route::post('open-device/equipment-confirm', ['uses'=>'OpenDeviceController@postEquipmentConfirm','as'=>'wechat.open-device.EquipmentConfirm']);
		Route::post('open-device/close-device', ['uses'=>'OpenDeviceController@postCloseDevice','as'=>'wechat.open-device.CloseDevice']);

		Route::get('/open-device/open-device-manage', ['uses'=>'OpenDeviceController@getOpenDeviceManage','as'=>'wechat.open-device.OpenDeviceManage']);
		Route::get('/open-device/history-list', ['uses'=>'OpenDeviceController@getHistoryList','as'=>'wechat.open-device.HistoryList']);

		Route::get('/open-laboratory/open-laboratory-manage', ['uses'=>'OpenLaboratoryController@getOpenLaboratoryManage','as'=>'wechat.open-laboratory.OpenLaboratoryManage']);


		// /msc/wechat/personal-center/index
	});
	Route::group(['prefix'=>'wechat','namespace'=>'WeChat'],function(){

		Route::controller('user', 'UserController');
		// /msc/wechat/personal-center/index
	});
});