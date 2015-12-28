<?php namespace Modules\Osce\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Modules\Osce\Repositories\Factory;
use Illuminate\Support\Facades\DB;
class OsceController extends Controller {

	//构造函数
	public function  __construct(){

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

	/**
	 * 打印当前语句SQL语句
	 * @author jiangzhiheng
	 * 2015.12.28 13:43
	 */
	public function start_sql($type){
		if($type == 1){
			return DB::connection("msc_mis_1")->enableQueryLog();
		} elseif ($type == 2) {
			return DB::connection("osce_mis")->enableQueryLog();
		} else{
			return DB::connection("sys_mis")->enableQueryLog();
		}

	}

	public function end_sql($type){
		if($type == 1){
			dd(DB::connection("msc_mis_1")->getQueryLog());
		} elseif ($type == 2) {
			dd(DB::connection("osce_mis")->getQueryLog());
		} else{
			dd(DB::connection("sys_mis")->getQueryLog());
		}

	}

	/**
	 * 基础的插入方法
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