<?php namespace Modules\Msc\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use DB;
class MscController extends Controller {

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
	 * weihuiguo
	 * 2015年12月22日10:37:44
	 */

	public function start_sql($type){
		if($type == 1){
			return DB::connection("msc_mis")->enableQueryLog();
		}else{
			return DB::connection("sys_mis")->enableQueryLog();
		}

	}

	public function end_sql($type){
		if($type == 1){
			dd(DB::connection("msc_mis")->getQueryLog());
		}else{
			dd(DB::connection("sys_mis")->getQueryLog());
		}

	}
	//判斷學生類別
	public function checkUserType($user_id){

		$uid = $user_id;
		$stu = Student::where('id','=',$uid)->first();
		if($stu){
			return 2;
		}else{
			$tea = Teacher::where('id','=',$uid)->first();
			if($tea){
				return  1;
			}else{
				return '';
			}
		}
	}

	public function error($status=0,$info='error'){
		$data = [
			'status'			=>	$status,
			'info'		=>	$info,
		];
		return $data;
	}
}