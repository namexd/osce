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

		//科室路由组
		Route::post('dept/add-dept', ['uses'=>'DeptController@AddDept','as'=>'msc.Dept.AddDept']);
		Route::post('dept/update-dept', ['uses'=>'DeptController@UpdateDept','as'=>'msc.Dept.UpdateDept']);
		Route::post('dept/del-dept', ['uses'=>'DeptController@DelDept','as'=>'msc.Dept.DelDept']);
		Route::get('dept/select-dept', ['uses'=>'DeptController@SelectDept','as'=>'msc.Dept.SelectDept']);
		Route::get('dept/pid-get-dept', ['uses'=>'DeptController@PidGetDept','as'=>'msc.Dept.PidGetDept']);
		Route::get('dept/dept-list', ['uses'=>'DeptController@DeptList','as'=>'msc.Dept.DeptList']);

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
		Route::post('floor/add-floor-insert', ['uses'=>'FloorController@postAddFloorInsert','as'=>'msc.admin.floor.postAddFloorInsert']);

		Route::post('floor/edit-floor-insert', ['uses'=>'FloorController@postEditFloorInsert','as'=>'msc.admin.floor.postEditFloorInsert']);
		Route::get('floor/stop-floor', ['uses'=>'FloorController@getStopFloor','as'=>'msc.admin.floor.getStopFloor']);
		Route::get('floor/delete-floor', ['uses'=>'FloorController@getDeleteFloor','as'=>'msc.admin.floor.getDeleteFloor']);
		Route::get('floor/stop-floor', ['uses'=>'FloorController@getStopFloor','as'=>'msc.admin.floor.getStopFloor']);
		Route::get('floor/delete-floor', ['uses'=>'FloorController@getDeleteFloor','as'=>'msc.admin.floor.getDeleteFloor']);
         //专业表路由
		 //Route::controller('profession','ProfessionController');
		 Route::get('profession/profession-list',['uses'=>'ProfessionController@getProfessionList','as'=>'msc.admin.profession.ProfessionList']);
		 Route::post('profession/profession-add',['uses'=>'ProfessionController@postProfessionAdd','as'=>'msc.admin.profession.ProfessionAdd']);
		 Route::get('profession/profession-edit/{id}',['uses'=>'ProfessionController@getProfessionEdit','as'=>'msc.admin.profession.ProfessionEdit']);
		 Route::post('profession/profession-save',['uses'=>'ProfessionController@postProfessionSave','as'=>'msc.admin.profession.ProfessionSave']);
		 Route::get('profession/profession-status',['uses'=>'ProfessionController@getProfessionStatus','as'=>'msc.admin.profession.ProfessionStatus']);
		 Route::get('profession/profession-deletion',['uses'=>'ProfessionController@getProfessionDeletion','as'=>'msc.admin.profession.ProfessionDeletion']);
		 Route::post('profession/profession-import',['uses'=>'ProfessionController@postProfessionImport','as'=>'msc.admin.profession.ProfessionImport']);
		//实验室路由
		Route::get('laboratory/index', ['uses'=>'LaboratoryController@index','as'=>'msc.admin.laboratory.index']);
		Route::post('laboratory/local', ['uses'=>'LaboratoryController@getLocal','as'=>'msc.admin.laboratory.getLocal']);
		Route::post('laboratory/floor', ['uses'=>'LaboratoryController@getFloor','as'=>'msc.admin.laboratory.getFloor']);
		Route::post('laboratory/add-lab-insert', ['uses'=>'LaboratoryController@getAddLabInsert','as'=>'msc.admin.laboratory.getAddLabInsert']);
		Route::post('laboratory/local', ['uses'=>'LaboratoryController@getLocal','as'=>'msc.admin.laboratory.getLocal']);
		Route::post('laboratory/floor', ['uses'=>'LaboratoryController@getFloor','as'=>'msc.admin.laboratory.getFloor']);
		Route::get('laboratory/delete-lab', ['uses'=>'LaboratoryController@getDeleteLab','as'=>'msc.admin.laboratory.getDeleteLab']);
		Route::get('laboratory/stop-lab', ['uses'=>'LaboratoryController@getStopLab','as'=>'msc.admin.laboratory.getStopLab']);
		Route::post('laboratory/edit-lab-insert', ['uses'=>'LaboratoryController@getEditLabInsert','as'=>'msc.admin.laboratory.getEditLabInsert']);
		Route::get('laboratory/lab-clearnder', ['uses'=>'LaboratoryController@getLabClearnder','as'=>'msc.admin.laboratory.getLabClearnder']);
		Route::get('laboratory/floor-lab', ['uses'=>'LaboratoryController@getFloorLab','as'=>'msc.admin.laboratory.getFloorLab']);
		Route::post('laboratory/operating-lab-cleander', ['uses'=>'LaboratoryController@postOperatingLabCleander','as'=>'msc.admin.laboratory.postOperatingLabCleander']);
		Route::get('laboratory/edit-lab-cleander', ['uses'=>'LaboratoryController@getEditLabCleander','as'=>'msc.admin.laboratory.getEditLabCleander']);
		Route::get('laboratory/lab-order-list', ['uses'=>'LaboratoryController@getLabOrderList','as'=>'msc.admin.laboratory.getLabOrderList']);
		Route::get('laboratory/lab-order-show', ['uses'=>'LaboratoryController@getLabOrderShow','as'=>'msc.admin.laboratory.getLabOrderShow']);
		//资源路由
		Route::controller('resources','ResourcesController');
		Route::get('resources/resources-index',['uses'=>'ResourcesController@getResourcesIndex','as'=>'msc.admin.resources.ResourcesIndex']);
		Route::post('resources/resources-add',['uses'=>'ResourcesController@postResourcesAdd','as'=>'msc.admin.resources.ResourcesAdd']);
		Route::get('resources/resources-edit/{id}',['uses'=>'ResourcesController@postResourcesEdit','as'=>'msc.admin.resources.ResourcesEdit']);
		Route::post('resources/resources-save',['uses'=>'ResourcesController@postResourcesSave','as'=>'msc.admin.resources.ResourcesSave']);
		Route::get('resources/resources-status/{id}',['uses'=>'ResourcesController@postResourcesStatus','as'=>'msc.admin.resources.ResourcesStatus']);
		Route::get('resources/resources-remove/{id}',['uses'=>'ResourcesController@getResourcesRemove','as'=>'msc.admin.resources.ResourcesRemove']);

		//职称路由
		Route::controller('professionaltitle','ProfessionalTitleController');
		Route::get('professionaltitle/job-title-index',['uses'=>'ProfessionalTitleController@getJobTitleIndex','as'=>'msc.admin.professionaltitle.JobTitleIndex']);
		Route::post('professionaltitle/holder-add',['uses'=>'ProfessionalTitleController@postHolderAdd','as'=>'msc.admin.professionaltitle.HolderAdd']);
		Route::get('professionaltitle/holder-edit/{id}',['uses'=>'ProfessionalTitleController@getHolderEdit','as'=>'msc.admin.professionaltitle.HolderEdit']);
		Route::post('professionaltitle/holder-save',['uses'=>'ProfessionalTitleController@postHolderSave','as'=>'msc.admin.professionaltitle.HolderSave']);
		Route::get('professionaltitle/holder-status/{id}',['uses'=>'ProfessionalTitleController@getHolderStatus','as'=>'msc.admin.professionaltitle.HolderStatus']);
		Route::get('professionaltitle/holder-remove',['uses'=>'ProfessionalTitleController@getHolderRemove','as'=>'msc.admin.professionaltitle.HolderRemove']);

		//实验室资源维护路由
		//Route::controller('LadMaintain','LadMaintainController');
		Route::get('ladMaintain/laboratory-list',['uses'=>'LadMaintainController@getLaboratoryList','as'=>'msc.admin.LadMaintain.LaboratoryList']);
		Route::get('ladMaintain/laboratory-list-data',['uses'=>'LadMaintainController@getLaboratoryListData','as'=>'msc.admin.LadMaintain.LaboratoryListData']);
		Route::get('/ladMaintain/laboratory-device-list',['uses'=>'LadMaintainController@getLaboratoryDeviceList','as'=>'msc.admin.LadMaintain.LaboratoryDeviceList']);
		Route::post('ladMaintain/devices-add',['uses'=>'LadMaintainController@postDevicesAdd','as'=>'msc.admin.LadMaintain.DevicesAdd']);
		Route::get('ladMaintain/devices-total-edit',['uses'=>'LadMaintainController@getDevicesTotalEdit','as'=>'msc.admin.LadMaintain.DevicesTotalEdit']);
		Route::get('ladMaintain/floor-lab', ['uses'=>'LadMaintainController@getFloorLab','as'=>'msc.admin.ladMaintain.getFloorLab']);
		Route::get('ladMaintain/lad-devices-deletion',['uses'=>'LadMaintainController@getLadDevicesDeletion','as'=>'msc.admin.LadMaintain.LadDevicesDeletion']);
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

		//待预约列表
		Route::get('/laboratory/laboratory-list',['uses'=>'LaboratoryCotroller@LaboratoryList','as'=>'msc.Laboratory.LaboratoryList']);
		//获取实验室待预约列表数据
		Route::get('/laboratory/laboratory-list-data',['uses'=>'LaboratoryCotroller@LaboratoryListData','as'=>'msc.Laboratory.LaboratoryListData']);
		//获取开放实验室待预约列表数据
		Route::get('/laboratory/open-laboratory-list-data',['uses'=>'LaboratoryCotroller@OpenLaboratoryListData','as'=>'msc.Laboratory.OpenLaboratoryListData']);
		//根据实验室id与时间 进入实验室预约填写表单页面
		Route::get('/laboratory/apply-laboratory',['uses'=>'LaboratoryCotroller@ApplyLaboratory','as'=>'msc.Laboratory.ApplyLaboratory']);
		//根据实验室id与时间 进入开放实验室预约日历安排页面
		Route::get('/laboratory/apply-open-laboratory',['uses'=>'LaboratoryCotroller@ApplyOpenLaboratory','as'=>'msc.Laboratory.ApplyOpenLaboratory']);
		//开放实验填写预约表单页面
		Route::post('/laboratory/open-laboratory-form',['uses'=>'LaboratoryCotroller@OpenLaboratoryForm','as'=>'msc.Laboratory.OpenLaboratoryForm']);
		//处理开放实验预约表单
		Route::post('/laboratory/open-laboratory-form-op',['uses'=>'LaboratoryCotroller@OpenLaboratoryFormOp','as'=>'msc.Laboratory.OpenLaboratoryFormOp']);

		// /msc/wechat/personal-center/index
	});
	Route::group(['prefix'=>'wechat','namespace'=>'WeChat'],function(){

		Route::controller('user', 'UserController');
		// /msc/wechat/personal-center/index
	});
});
