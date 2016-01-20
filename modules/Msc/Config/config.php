<?php

return [
	'name' => 'Msc',
	//导入excl 时，中英文字段名对照 组
	'importForCnToEn'=>[
		//课程清单 中英文 字段名对照
		'courses'=>[
				'课程号'=>'code',
				'课程名称'=>'name',
		],
		//课程表导入中英文字段名对照
		'coursesPlan'=>[
			"课程号" => "course_code",
			"胸牌号" => "teahcer_code",
			"日期" => 'currentdate',
			"上课时间" => 'begintime',
			"下课时间" => 'endtime',
			"教室编号" => 'calss_room_code',
		],
		// 培训安排
		'training_plan' => [
			'分组'             => 'group',
			'培训课程代码'     => 'course_code',
			'技能中心负责老师' => 'manager_name',
			'培训地点代码'     => 'address_code',
			'上课老师'         => 'teacher',
			'上课老师胸牌号'   => 'teacher_code',
			'培训开始时间'     => 'begin_dt',
			'培训结束时间'     => 'end_dt',
		],
		// 培训分组
		'training_group' => [
			'姓名' => 'name',
			'手机' => 'mobile',
			'分组' => 'group',
		],
       //导入学生的组
		'student_group'=>[
			'姓名'=>'name',
			'学号'=>'code',
			'年级'=> 'grade',
			'专业'=> 'professional',
			'类别'=> 'student_type',
			'手机号'=>'mobile',
			'证件号'=>'idcard',
			'性别'=> 'gender',
			'状态'=>'status',
		],
		//导入教师的组
		'teacher_group'=>[
			'姓名'=>'name',
			'胸牌号'=>'code',
			'科室'=> 'teacher_dept',
			'手机号'=>'mobile',
			'性别'=> 'gender',
			'角色'=>'role',
			'状态'=>'status',
		],
			'user'=>[
					'职工号'=>'code',
					'姓名'=>'name',
					"性别" => 'sex',
			],


		'lab_import'	=>	[
			"楼层" => "floor",
			"房号（地址）" => 'code',
			"用房名称" => "name",
			"使用面积" => 'area',
			"实验室类型" => "type",
			"管理教师" => "manager_name",
			"实验室类型代码" => 'typeCode',
		],
       //导入专业的组
		'profession_group' =>[
			'专业代码' => 'name',
			'专业名称' => 'code',
			'状态'    => 'status',
		]

	],
	'idcard_type'  	=>		[	1=>'身份证',	2=>'护照'],
	'page_size'		=>		10,
	'video_host'	=>		'192.168.1.200',
	'video_port'	=>		'9090',
	'student_type'  =>      [1=>'本科', 2=>'专科'],
	'user_status'   =>      [1=>'正常', 2=>'禁用', 3=>'删除'],
	'profession_status' =>  [1=>'正常', 2=>'停用']
];