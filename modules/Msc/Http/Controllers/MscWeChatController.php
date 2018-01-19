<?php namespace Modules\Msc\Http\Controllers;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
class MscWeChatController extends Controller {

	public function  __construct(Student $student,Teacher $teacher){
		$user = Auth::user();
		if ($user) {
            $uid = @$user->id;
            $user->user_type = $this->checkUserType($uid);
        }
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

	//获取OpenID
	public function getOpenId(){
		$auth = new \Overtrue\Wechat\Auth(config('wechat.app_id'), config('wechat.secret'));
		$userInfo = $auth->authorize($to = null, $scope = 'snsapi_userinfo', $state = 'STATE');
		if(!empty($userInfo)){
			return $userInfo->openid;
		}else{
			return false;
		}
	}

	//像微信用户发送普通文本消息
	public function sendMsg($msg,$openid){
		$userService = new \Overtrue\Wechat\Staff(config('wechat.app_id'), config('wechat.secret'));
		return $userService->send($msg)->to($openid);

	}
	//生成js SDK 配置
	public function GenerationJsSdk(){
		return new \Overtrue\Wechat\Js(config('wechat.app_id'), config('wechat.secret'));
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
}