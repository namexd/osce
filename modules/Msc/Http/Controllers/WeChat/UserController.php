<?php namespace Modules\Msc\Http\Controllers\WeChat;

use Illuminate\Support\Facades\Input;
use Modules\Msc\Http\Controllers\MscWeChatController;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Extensions\OAuth\PasswordGrantVerifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
class UserController extends MscWeChatController {

	//用户登录
	public function getUserLogin()
	{

		//$openid = $this->getOpenId();

		Session::put('openid','dfsafas');

		return view('msc::wechat.user.login');
	}

	//处理用户登录
	public function postUserLoginOp(Request $request,PasswordGrantVerifier $passwordGrantVerifier)
	{
		$requests = $request->all();
		$rew = $passwordGrantVerifier->verify($requests['username'],$requests['password']);
		if($rew){
			$user = Auth::user();
			//$user->user_type = $this->checkUserType($user->id);

			if(!empty($user['mobile'])){
				return redirect()->intended('/msc/wechat/personal-center/index');
			}else{
				return redirect()->intended('/msc/wechat/user/user-binding');
			}
		}else{
			Session::put('openid','');
			return redirect()->intended('/msc/wechat/user/user-login');
		}

	}

	//用户注册
	public function getUserRegister(UserRepository $userRepository)
	{
		//获取专业  关键字搜索  keyword  关键字(必须的) page
		//$userRepository->getProfessionalList();
		//获取科室列表 关键字搜索 keyword  关键字(必须的) page
		//$userRepository->getTeacherDeptList();

		$data = [
			//年级列表
			'GreadeList'=>$userRepository->getGreadeList(),
			//学生类型列表
			'StudentTypeList'=>$userRepository->getStudentTypeList()
		];


		return view('msc::wechat.user.register',$data);
		//$GreadeList
	}
	/**
	 * 学生注册
	 *
	 * @access public
	 * @param
	 * * string		name			姓名(必须的)
	 * * string		mobile			手机号(必须的)
	 * * string		password		密码(必须的)
	 * * int		gender			性别(必须的)  性别 1=男 2=女 0=未知
	 * * string		code			学号(必须的)
	 * * int		student_type	学生类型(必须的)
	 * * int		idcard_type		身份证件类型(必须的)
	 * * string		idcard			身份证明编号(必须的)
	 * * int 		class			班级
	 * * int		grade			年级(必须的)
	 * * int		professional	专业(必须的)
	 * * string		openid			微信OpenID
	 *	Category
	 * @return 用户ID
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-19 17:32
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 */
	//处理学生注册
	public function postRegStudentOp(Request $request,UserRepository $userRepository)
	{
		if(empty($request->idcard_type) && $request->idcard_type == 1){
			$request->idcard = $request->idcard;
		}elseif(empty($request->idcard_type) &&  $request->idcard_type == 2){
			$request->idcard = $request->idcard2;
		}
		$request['class'] = 1;
		$request['gender'] =$request['sex2'];
		$this->validate($request,[
			'name'=>'required',
			'code'=>'required',
			'password'=>'required',
			'mobile'=>'required',
			'student_type'=>'required',
			'idcard_type'=>'required',
			'idcard'=>'required',
			'class'=>'required',
			'grade'=>'required',
			'professional'=>'required',
			'gender'=>'required'
		]);

		$request['openid'] = \Illuminate\Support\Facades\Session::get('openid','');
		if($userRepository->regStudent($request))
			return redirect()->intended('/msc/wechat/user/user-login');
		else{
			return view('msc::wechat.index.index_error',array('error_msg'=>'注册失败'));
		}

	}

	/**
	 * 教师注册
	 *
	 * @access public
	 * @param
	 * * string		name			姓名(必须的)
	 * * string		password		密码(必须的)
	 * * string		mobile			手机号(必须的)
	 * * string		code			工号(必须的)
	 * * int		teacher_dept	科室(必须的)
	 * * int		gender			性别(必须的)  性别 1=男 2=女 0=未知
	 *
	 * @return 用户ID
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-19 17:37
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 */

	//处理老师注册
	public function postRegTeacherOp(Request $request,UserRepository $userRepository)
	{
		$this->validate($request,[
			'name'=>'required',
			'code'=>'required|integer',
			'password'=>'required',
			'mobile'=>'required|mobile_phone',
			'teacher_dept'=>'required|integer',
			'gender'=>'required|integer'
		]);
		$request['openid'] = \Illuminate\Support\Facades\Session::get('openid','');
		if($userRepository->regTeacher($request))
			return redirect()->intended('/msc/wechat/user/user-login');
		else
			return view('msc::wechat.index.index_error',array('error_msg'=>'注册失败'));
	}
	//用户绑定(登录绑定)
	public function getUserBinding()
	{
		$user = Auth::user();
		$data = [
			'user'=>$user
		];
		return view('msc::wechat.user.binding',$data);
	}

	/**
	 * 绑定用户手机号(初始绑定用户手机号及修改初始密码)
	 * @access public
	 *
	 * @param
	 * * int           id          用户ID(必须的)
	 * * string        password     用户密码(必须的)
	 * * string        mobile       手机号码(必须的)
	 *
	 * @return json ['data'=>{'id':用户id,username:用户名,mobile：用户修改后的手机号码}]
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06 16:41
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */

	//用户绑定(登录绑定)
	public function postUserBindingOp(UserRepository $userRepository,Request $request)
	{
		$this->validate($request,[
			'password'=>'required',
			'mobile'=>'required',
			'id'=>'required',
		]);
		$rew = $userRepository->relativeMobile($request->all());
		if($rew){
			return redirect()->intended('/msc/wechat/user/user-login');
		}else{
			return view('msc::wechat.index.index_error',array('error_msg'=>'绑定失败'));
		}
	}

	//根据关键字获取 科室信息
	public function getTeacherDeptList(UserRepository $userRepository){
		$keyword = Input::get('keyword');
		$item = $userRepository->getTeacherDeptList($keyword);
		print_r($item[0]);
	}

}