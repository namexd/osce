<?php
namespace App\Config;

return [
	'name' => 'App',
	'ModuleSchedulesList'=>[
        'Osce'  =>  \Modules\Osce\Commands\DefaultCommand::class
    ],
];