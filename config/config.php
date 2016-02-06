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
];