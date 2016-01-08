<?php
namespace Modules\Osce\Config;

return [
	'name' => 'Osce',
	'page_size' => 10,
	'order_type' => 'created_at',
	'order_by' => 'desc',

	'importForCnToEn'=>[
		// 中英文 字段名对照
		'subject'=>[
			'考核名称'=>'name',
			'考核描述'=>'description',
			'考核序号'=>'sort',
			'考核满分'=>'score',
			'考核状态'=>'status',
			'考核创建人'=>'42',
			'考核描述'=>'description'
		],
	]
];