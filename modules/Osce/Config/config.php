<?php
namespace Modules\Osce\Config;

return [
	'name' => 'Osce',
	'page_size' => 10,
	'order_type' => 'created_at',
	'order_by' => 'desc',

	'importForCnToEn'=>[
		// 中英文 字段名对照
		'standard'=>[
			'序号'=>'sort',
			'考核点'=>'check_point',
			'考核项'=>'check_item',
			'评分标准'=>'answer',
			'分数'=>'score',
			'父级序号'=>'pid',
			'层级'=>'level'
		],
		'teacher'=>[
			//暂时空置
		],
		'student'=>[
			'姓名'		=>	'name',
			'性别'		=>	'gender',
			'学号'		=>	'code',
			'身份证号'	=>	'idcard',
			'联系电话'	=>	'mobile',
			'电子邮箱'	=>	'email',
			'头像'		=>	'avatar',
			'备注'		=>	'level'
		],
	],
	'machine_category'=>[
		1 =>[
			'id'=>1,
			'name'=>'摄像机',
		],
		2 =>[
			'id'=>2,
			'name'=>'Pad',
		],
		3 =>[
			'id'=>3,
			'name'=>'Watch',
		]
	],
	'manager'=>[
		1
	],
	'prepare'	=>	10
];