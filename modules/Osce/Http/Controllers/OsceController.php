<?php namespace Modules\Osce\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Modules\Osce\Repositories\Factory;
use Illuminate\Support\Facades\DB;
class OsceController extends Controller {

	/**
	 * 返回成功的json数据
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
	 * 返回多行成功后的json数据
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
	 * 返回失败的json数据
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
			'message'		=>	'δ֪???:'.$ex->getMessage(),
		];
	}



	/**
	 * 通用的create方法
	 * @param $request
	 * @param $model
	 * @return bool
	 */
	public function create($request,$model)
	{
		DB::beginTransaction;
		$result =  DB::table($model) -> create($request);
		if (!$result) {
			DB::rollback();
			return false;
		}
		DB::commit();
		return $result;
	}
}