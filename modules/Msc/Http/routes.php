<?php



Route::group(['prefix' => 'msc', 'namespace' => 'Modules\Msc\Http\Controllers'], function()
{
	Route::get('/', 'MscController@index');

});

Route::group(['prefix' => "msc",'namespace' => 'Modules\Msc\Http\Controllers','middleware' => []], function()
{

	//http://www.mis.hx/msc/admin/resources-manager/user-register
	Route::group(['prefix'=>'admin','namespace'=>'Admin'],function(){
		//测试路由
		Route::get('test/index', ['uses'=>'TestController@Index','as'=>'msc.Test.Index']);

		Route::controller('verify', 'VerifyController');
		Route::get('verify/student/{status?}', ['uses'=>'VerifyController@getStudent','as'=>'msc.verify.student']);
		Route::get('verify/teacher/{status?}', ['uses'=>'VerifyController@getTeacher','as'=>'msc.verify.teacher']);

		Route::post('verify/change-users-status',['uses'=>'VerifyController@postChangeUsersStatus','as'=>'verify.postChangeUsersStatus']);

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
		//学生用户导入导出
		Route::get('user/export-student-user',['uses'=>'UserController@getExportStudentUser','as'=>'msc.admin.user.ExportStudentUser']);
		Route::post('user/import-student-user',['uses'=>'UserController@postImportStudentUser','as'=>'msc.admin.user.ImportStudentUser']);
		Route::post('user/student-info',['uses'=>'UserController@getStudentInfo','as'=>'msc.admin.user.StudentInfo']);

		Route::get('user/teacher-edit/{id}', ['uses'=>'UserController@getTeacherEdit','as'=>'msc.admin.user.TeacherEdit']);
		Route::get('user/teacher-trashed/{id}', ['uses'=>'UserController@getTeacherTrashed','as'=>'msc.admin.user.TeacherTrashed']);
		Route::get('user/teacher-status/{id}', ['uses'=>'UserController@getTeacherStatus','as'=>'msc.admin.user.TeacherStatus']);

		Route::post('user/teacher-add', ['uses'=>'UserController@postTeacherAdd','as'=>'msc.admin.user.TeacherAdd']);
		Route::post('user/teacher-save', ['uses'=>'UserController@postTeacherSave','as'=>'msc.admin.user.TeacherSaveEdit']);

		//教师用户导出，导入
		Route::get('user/export-teacher-user', ['uses'=>'UserController@getExportTeacherUser','as'=>'msc.admin.user.ExportTeacherUser']);
		Route::post('user/import-teacher-user', ['uses'=>'UserController@getImportTeacherUser','as'=>'msc.admin.user.ImportTeacherUser']);
		Route::get('user/teacher-info', ['uses'=>'UserController@getTeacherInfo','as'=>'msc.admin.user.TeacherInfo']);
		//楼栋路由
		Route::get('floor/index', ['uses'=>'FloorController@index','as'=>'msc.admin.floor.index']);
		Route::get('laboratory/index', ['uses'=>'LaboratoryController@index','as'=>'msc.admin.laboratory.index']);
	});

//	Route::group(['prefix'=>'wechat','namespace'=>'WeChat','middleware' => ['wechatauth']],function(){
	Route::group(['prefix'=>'wechat','namespace'=>'WeChat'],function(){

		Route::controller('user', 'UserController');
		Route::get('user/check-code-register',['uses'=>'UserController@getCheckCodeRegister','as'=>'msc.user.getCheckCodeRegister']);
		Route::controller('personal-center', 'PersonalCenterController');
		Route::get('personal-center/cancel-open-device-apply',['uses'=>'PersonalCenterController@getCancelOpenDeviceApply','as'=>'msc.personalCenter.cancelOpenDeviceApply']);
		//开放设备当前预约
		Route::get('personal-center/my-apply',['uses'=>'PersonalCenterController@getMyApply','as'=>'msc.wechat.personalCenter.getMyApply']);
		//开放设备使用历史
		Route::get('personal-center/user-open-device-histroy-data',['uses'=>'PersonalCenterController@getUserOpenDeviceHistroyData','as'=>'msc.personalCenter.userOpenDeviceHistroyData']);
		//开放设备取消预约
		Route::get('personal-center/cancel-open-device-apply',['uses'=>'PersonalCenterController@getCancelOpenDeviceApply','as'=>'msc.personalCenter.cancelOpenDeviceApply']);
		Route::get('personal-center/info-manage',['uses'=>'PersonalCenterController@getInfoManage','as'=>'msc.personalCenter.infoManage']);
		//我的課程
		Route::get('personal-center/my-course',['uses'=>'PersonalCenterController@getMyCourse','as'=>'msc.personalCenter.MyCourse']);

		//我的开放实验室预约
		Route::get('/personal-center/my-opening-laboratory',['uses'=>'PersonalCenterController@getMyOpeningLaboratory','as'=>'msc.personalCenter.getMyOpeningLaboratory']);

		//取消预约
		Route::get('/personal-center/cancel-laboratory',['uses'=>'PersonalCenterController@getCancelLaboratory','as'=>'msc.personalCenter.getCancelLaboratory']);



		// /msc/wechat/personal-center/index
	});
	Route::group(['prefix'=>'wechat','namespace'=>'WeChat'],function(){

		Route::controller('user', 'UserController');
		// /msc/wechat/personal-center/index
	});
});