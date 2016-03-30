<?php
namespace Modules\Osce\Config;

return [
	'name' => 'Osce',
	'page_size' => 10,
	'order_type' => 'created_at',
	'order_by' => 'desc',
    'num'    =>10,
    'student_num'    =>20,
	'begin_dt_buffer' => 10,
	'batch_num' => 3,
	'wait_student_num' => 4,

	'topticOptionMaxNumer'	=>	15,

	'importForCnToEn'=>[
		// 中英文 字段名对照
		'standard'=>[
			'序号'=>'sort',
			'考核点'=>'check_point',
			'考核项'=>'check_item',
			'评分标准'=>'answer',
			'分数'=>'score',
		],
		'teacher'=>[
			'姓名'		=>	'name',
			'性别'		=>	'gender',
			'教师编号'	=>	'code',
			'身份证号'	=>	'idcard',
			'联系电话'	=>	'mobile',
			'教师类别'	=>  'type',
			'电子邮箱'	=>	'email',
			'头像'		=>	'avatar',
			'备注'		=>	'description',
		],
		'student'=>[
			'姓名'		=>	'name',
			'性别'		=>	'gender',
			'学号'		=>	'code',
			'身份证号'	=>	'idcard',
			'联系电话'	=>	'mobile',
			'电子邮箱'	=>	'email',
			'头像'		=>	'avator',
			'备注'		=>	'description',
			'准考证号'	=>  'exam_sequence',
			'班级'		=>	'grade_class',
			'班主任姓名' =>	'teacher_name'
		],
	],
	'machine_category'=>[
		1 =>[
			'id'=>1,
			'name'=>'摄像机',
		],
		2 =>[
			'id'=>2,
			'name'=>'PAD',
		],
		3 =>[
			'id'=>3,
			'name'=>'腕表',
		]
	],
	'manager'=>[
		0
	],
	'prepare'			=>	10,
	'spRoleId'			=>	4,

	'invigilatorRoleId'	=>	1,
	'studentRoleId'		=>	2,
	'adminRoleId'		=>	3,
	'superRoleId'		=>	5,
	'patrolRoleId'		=>	6,		//巡考老师角色 TODO: Zhoufuxiang  2016-3-23

	//场所类型的管理
	'room_cate' => [
		0	=>	'考场',
		1	=>	'中控室',
		2	=>	'走廊',
		3	=>	'侯考区'
	]
];