<?php

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use Log;

/**
 * @ignore
 */
class ApiBaseController extends Controller {


	/**
	 * @ignore
	 */
	public function __construct(Request $request){
		Log::debug($request->fullUrl(),$request->all());
	}

	/**
	 * 接口调用成功返回json数据结构
	 *
	 * @return string
	 *
	 * [
	 * 	'code'			=>	1,
	 * 	'message'		=>	'success',
	 *	'data'			=>	''
	 * ];
	 *
	 */
	public function success_data($data=[],$code=1,$message='success'){
		return [
			'code'			=>	$code,
			'message'		=>	$message,
			'data'			=>	$data
		];
	}

	/**
	 * 接口调用成功返回json数据结构(多行记录)
	 *
	 * @return string
	 * [
	 * 		'code'			=>	1,
	 * 		'message'		=>	'success',
	 * 		'data'			=>	[
	 * 		'total'		=>	10,
	 * 		'pagesize'	=>	10,
	 * 		'pageindex'	=>	1,
	 * 		'rows'		=>	[]
	 * 		]
	 * ];
	 */
	public function success_rows($code=1,$message='success',$total=0,$pagesize=10,$pageindex=0,$rows=[]){
		return [
			'code'			=>	$code,
			'message'		=>	$message,
			'data'			=>	[
				'total'		=>	$total,
				'pagesize'	=>	$pagesize,
				'page'		=>	$pageindex,
				'rows'		=>	$rows
			]
		];
	}

	/**
	 * 接口调用失败返回json数据结构
	 *
	 * @return string
	 * [
	 *  	'code'			=>	-999,
	 * 		'message'		=>	'fail'
	 * 	];
	 */
	public function fail(\Exception $ex){
		return [
			'code'			=>	-999,
			'message'		=>	'未知异常:'.$ex->getMessage(),
		];
	}
	
}