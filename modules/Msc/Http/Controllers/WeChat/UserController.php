<?php namespace Modules\Msc\Http\Controllers\WeChat;

use Illuminate\Support\Facades\Input;
use Modules\Msc\Http\Controllers\MscController;
use App\Repositories\UserRepository;
use Modules\Msc\Entities\StdProfessional;
use Illuminate\Http\Request;
use App\Extensions\OAuth\PasswordGrantVerifier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use App\Entities\User;
use Modules\Msc\Entities\ProfessionalTitle;
class UserController extends MscController {

	/**
	 * 用户登录
	 * @method get /msc/wechat/user/user-login
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>get请求字段：</b>
	 * @return view
	 *
	 * @version 0.1
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-12-8 16:20
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */
	public function getUserLogin()
	{

		$getOpenid = env('OPENID', true);

		if($getOpenid){
			$openid = \Illuminate\Support\Facades\Session::get('openid','');
			if(empty($openid)){
				$openid = $this->getOpenId();
				Session::put('openid',$openid);
			}
		}else{
			Session::put('openid','dfdsfds');
		}
		return view('msc::wechat.user.login');

	}

	/**
	 * 处理用户登录
	 * @method post /msc/wechat/user/user-login-op
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>get请求字段：</b>
	 * @return view
	 *
	 * @version 0.1
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-12-8 16:20
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */
	public function postUserLoginOp(Request $request,PasswordGrantVerifier $passwordGrantVerifier)
	{
		$requests = $request->all();
		$rew = $passwordGrantVerifier->verify($requests['username'],$requests['password']);
		if($rew){
			$user = Auth::user();
			$user->user_type = $this->checkUserType($user->id);
			if(!empty($user->user_type)){
				$userInfo = [];
				if($user->user_type == 1){
					$userInfo = Teacher::where('id','=',$user->id)->first();
				}elseif($user->user_type == 2){
					$userInfo = Student::where('id','=',$user->id)->first();
				}
				if($userInfo->validated == 1){
					if(!empty($user['mobile'])){
						$openid = \Illuminate\Support\Facades\Session::get('openid','');
						User::where('id','=',$user->id)->update(['openid'=>$openid]);
						return redirect()->intended('/msc/wechat/personal-center/index');
					}else{
						return redirect()->intended('/msc/wechat/user/user-binding');
					}
				}else{
					return view('msc::wechat.index.index_waiting');
				}
			}else{
				Session::put('openid','');
				return redirect()->intended('/msc/wechat/user/user-login');
			}
		}else{
			Session::put('openid','');
			return redirect()->intended('/msc/wechat/user/user-login');
		}

	}

	/**
	 * 用户注册
	 * @method post /msc/wechat/user/user-register
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>get请求字段：</b>
	 * @return view
	 *
	 * @version 0.1
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-12-8 16:20
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */
	public function getUserRegister(UserRepository $userRepository)
	{
		$ProfessionalTitle = new ProfessionalTitle;
		$StdProfessional = new StdProfessional;
		$data = [
			//职称
			'ProfessionalTitleList'=>$ProfessionalTitle->getProfessionalTitleList(),
			//年级列表
			'GreadeList'=>$userRepository->getGreadeList(),
			//学生类型列表
			'StudentTypeList'=>$userRepository->getStudentTypeList(),

			'StudentProfessionalList'=>$StdProfessional->getProfessionalList()
		];

		return view('msc::wechat.user.register',$data);

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
	 * @author Zouyuchao <Zouyuchao@sulida.com>
	 * @date 2015-11-19 17:32
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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
		//判断手机号码 有无注册
		if($this->CheckPhoneRegister($request['mobile'])){
			$request['openid'] = \Illuminate\Support\Facades\Session::get('openid','');
			if($this->CheckCodeRegister($request['code'])){
				if($userRepository->regStudent($request)){
					Session::put('openid','');
					return view('msc::wechat.index.index_waiting');
				}else{
					return view('msc::wechat.index.index_error',array('error_msg'=>'注册失败'));
				}
			}else{
				return view('msc::wechat.index.index_error',array('error_msg'=>'（胸牌/学号）已经被注册过'));
			}
		}else{
			return view('msc::wechat.index.index_error',array('error_msg'=>'该手机已经被注册'));
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
	 * @author Zouyuchao <Zouyuchao@sulida.com>
	 * @date 2015-11-19 17:37
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */

	//处理老师注册
	public function postRegTeacherOp(Request $request,UserRepository $userRepository)
	{

		$this->validate($request,[
			'name'=>'required',
			'code'=>'required|integer',
			'password'=>'required',
			'mobile'=>'required|mobile_phone',
			'gender'=>'required|integer',
			'professionalTitle'=>'required|integer',
			'professional'=>'required|integer'
		]);
		$request['openid'] = \Illuminate\Support\Facades\Session::get('openid','');
		//判断手机号码 有无注册
		if ($this->CheckPhoneRegister($request['mobile'])) {
			if ($this->CheckCodeRegister($request['code'])) {

				if ($userRepository->regTeacher($request))

					return redirect()->intended('/msc/wechat/user/user-login');
				else {
					return view('msc::wechat.index.index_error', array('error_msg' => '注册失败'));
				}
			} else {
				return view('msc::wechat.index.index_error', array('error_msg' => '（胸牌/学号）已经被注册过'));
			}
		} else {
			return view('msc::wechat.index.index_error', array('error_msg' => '该手机已经被注册'));
		}

	}
	/**
	 * 用户绑定(登录绑定)
	 * @method get /msc/wechat/user/user-binding
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>get请求字段：</b>
	 * @return view
	 *
	 * @version 0.1
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-12-8 16:20
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */
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
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-11-06 16:41
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
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

	/**
	 * 检测当前Code 有无被注册过
	 *
	 * @access private
	 *
	 * @param
	 * * string        code     胸牌号(必须的)
	 *
	 * @return
	 *
	 * @version 1.0
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-12-21 10:21
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 * use Modules\Msc\Entities\Student;
	   use Modules\Msc\Entities\Teacher;
	 */
	private function CheckCodeRegister($code)
	{
		$StudentInfo = Student::where('code','=',$code)->first();
		$TeacherInfo = Teacher::where('code','=',$code)->first();

		if(!empty($StudentInfo) || !empty($TeacherInfo)){
			return false;
		}else{
			return true;
		}
	}


	/**
	 * 检测当前Code 有无被注册过
	 *
	 * @access private
	 *
	 * @param
	 * * string        $mobile     手机号码(必须的)
	 *
	 * @return
	 *
	 * @version 1.0
	 * @author tangjun <tangjun@sulida.com>
	 * @date 2015-12-21 10:21
	 * @copyright  2013-2017 sulida.com  Inc. All Rights Reserved
	 */
	private function CheckPhoneRegister($mobile)
	{
		$UserInfo = User::where('mobile','=',$mobile)->first();

		if(!empty($UserInfo)){
			return false;
		}else{
			return true;
		}
	}





}