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
	'patrolRoleId'	=>	6,		//巡考老师角色 TODO: fandian  2016-3-23
	'username'=>[
		'SP病人',
		'考生',
		'超级管理员',
		'考试组织管理员',
		'监巡考老师',
	],
    'external_redis_host' => env('EXTERNAL_REDIS_HOST', env('REDIS_HOST', 'redis.sz-sulead.com')),
    'external_redis_port' => env('EXTERNAL_REDIS_PORT', env('REDIS_PORT', 60379)),
];