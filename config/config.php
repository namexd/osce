<?php
namespace App\Config;

return [
	'name' => 'App',
	'ModuleSchedulesList'=>[
        'Osce'  =>  \Modules\Osce\Commands\DefaultCommand::class
    ],
	//超级管理员角色ID
	'superRoleId'	=>	5,
	'spRoleId'		=>	4,
	'examineeRoleId'=>	2,
	'teacherRoleId'	=>	1,
	'username'=>[
		'SP病人',
		'考生',
		'超级管理员',
		'考试组织管理员',
		'监巡考老师',
	],
];