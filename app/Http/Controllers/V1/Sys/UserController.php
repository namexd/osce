<?php

namespace App\Http\Controllers\V1\Sys;

use Modules\Msc\Entities\Grade;
use Modules\Msc\Entities\StdClass;
use Modules\Msc\Entities\StdProfessional;
use Modules\Msc\Entities\StudentType;
use Modules\Msc\Entities\TeacherDept;
use Illuminate\Http\Request;
use App\Http\Controllers\V1\ApiBaseController;
use App\Entities\User;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use App\Entities\SysValidatecode;
use Log;
use DB;

/**
 * 用户操作接口
 *
 * @package User
 *
 */
class UserController extends ApiBaseController {

	/**
	 * 学生注册
	 *
	 * @api POST /api/1.0/public/msc/user/reg-student
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>post请求字段：</b>
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
	 *
	 * @return object
	 *
	 * @version 1.0
	 * @author limingyao <limingyao@misrobot.com>
	 * @date 2015-11-05
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 */
	public function postRegStudent(Request $request){
		$this->validate($request, [
			'name' 			=> 	'required|max:16|min:2',
			'mobile' 		=> 	'required|size:11|mobile_phone|unique:users,mobile',
			'gender'		=> 	'required|integer|min:0|max:2',
			'code' 			=> 	'required',
			'password' 		=> 	'required|max:18|min:6',
			'student_type'	=> 	'required',
			'idcard_type'	=> 	'required',
			'idcard'		=> 	'required',
			]);

		try{
			DB::beginTransaction();
			$form_user=$request->only('name','mobile','idcard','idcard_type','password','gender','openid');
			$form_user['username']=$request->get('mobile');
			$form_user['password']=$this->passwordHiiden($request->get('password'));
			$user=User::create($form_user);

			$form_student=$request->only('name','class','grade','professional','code','student_type');

			$form_student['id']=$user->id;
			$student=Student::create($form_student);
			DB::commit();
			return response()->json($this->success_data(['id'=>$user->id]));
		}
		catch(\Exception $ex){
			DB::rollback();
			return response()->json($this->fail($ex));
		}
	}

	/**
	 * 教师注册
	 *
	 * @api POST /api/1.0/public/msc/user/reg-teacher
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>post请求字段：</b>
	 * * string		name			姓名(必须的)
	 * * string		password		密码(必须的)
	 * * string		mobile			手机号(必须的)
	 * * string		code			工号(必须的)
	 * * int		teacher_dept	科室(必须的)
	 * * int		gender			性别(必须的)  性别 1=男 2=女 0=未知
	 *
	 * @return object
	 *
	 * @version 1.0
	 * @author limingyao <limingyao@misrobot.com>
	 * @date 2015-11-05
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 */
	public function postRegTeacher(Request $request){
		$this->validate($request, [
			'name' 			=> 	'required|max:16|min:2',
			'password' 		=> 	'required|max:18|min:6',
			'gender'		=> 	'required|integer|min:0|max:2',
			'mobile' 		=> 	'required|size:11|mobile_phone|unique:users,mobile',
			'code' 			=> 	'required',
			'teacher_dept'	=> 	'required',
		]);
		try{
			DB::beginTransaction();
			$form_user=$request->only('name','mobile','password','gender');
			$form_user['username']=$request->get('mobile');
			$form_user['password']=$this->passwordHiiden($request->get('password'));
			$user=User::create($form_user);

			$form_teacher=$request->only('name','teacher_dept','code');
			$form_teacher['id']=$user->id;
			Teacher::create($form_teacher);
			DB::commit();
			return response()->json($this->success_data(['id'=>$user->id]));
		}
		catch(\Exception $ex){
			DB::rollback();
			return response()->json($this->fail($ex));
		}
	}


	/**
	 * 获取班级列表
	 * @api GET /api/1.0/public/msc/user/class-list
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * * string		keyword			班级名(必须的)  e.g： 二   or  班
	 * * int		page			页码(必须的)
	 *
	 * @return json :['data'=>[{'id':'班级id','code':'班级编号','name':'班级编号'},{……}]]
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06 11:00
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getClassList(Request $request){
		$keywordData=$request->only(['keyword']);
		$keyword=e($keywordData['keyword']);
		if(strlen($keyword)>0)
		{
			$pagination=$StdClass=StdClass::where('name','like','%'.$keyword.'%')->paginate(20);
			$paginationArray=$pagination->toArray();
			return response()->json(
					$this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
			);
		}
	}

	/**
	 * 获取学生类型列表
	 * @api GET /api/1.0/public/msc/user/student-type-list
	 * @access public
	 *
	 * @return json [{id:1,name:本科生},{id:2,name:类别名}]
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-09
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getStudentTypeList(){
		$StudentType=New StudentType();
		$list=$StudentType->studentType;
		return response()->json(
				$this->success_rows(1,'获取成功',count($list),20,1,$list)
		);
	}
	/**
	 * 获取年级列表
	 * @api GET /api/1.0/public/msc/user/greade-list
	 * @access public
	 *
	 * @return json :['data'=>[{'id':年份，name:'年级'}，{'id':年份，name:'年级'}，……]}
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06 11:00
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getGreadeList(){
		$Grade=new Grade();
		$list=$Grade->getGradeList();
		return response()->json(
				$this->success_rows(1,'获取成功',count($list),20,1,$list)
		);
	}

	/**
	 * 获取专业列表
	 * @api GET /api/1.0/public/msc/user/professional-list
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>post请求字段：</b>
	 * * string        keyword        关键字(必须的)
	 * * string        page        		页码(必须的)
	 *
	 * @return json :['data'=>[{'id':'专业id','code':'专业编号','name':'专业名称'},{……}]]
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06 12:36
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getProfessionalList(Request $request){
		$keywordData=$request->only(['keyword']);
		$keyword=urldecode(e($keywordData['keyword']));
		if(strlen($keyword)>0)
		{
			$pagination=$StdClass=StdProfessional::where('name','like','%'.$keyword.'%')->paginate(20);
			$paginationArray=$pagination->toArray();
			return response()->json(
					$this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
			);
		}
        return '';
	}

	/**
	 * 获取科室列表
	 * @api GET /api/1.0/public/msc/user/teacher-dept-list
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>post请求字段：</b>
	 * * string        keyword        关键字(必须的)
 	 * * string        page        		页码(必须的)
	 *
	 * @return json :['data'=>[{'id':'科室id','code':'科室编号','name':'科室名称'},{……}]]
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06 12:42
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getTeacherDeptList(Request $request){
		$keywordData=$request->only(['keyword']);
		$keyword=e($keywordData['keyword']);
		if(strlen($keyword)>0)
		{
			$pagination=$StdClass=TeacherDept::where('name','like','%'.$keyword.'%')->paginate(20);
			$paginationArray=$pagination->toArray();
			return response()->json(
					$this->success_rows(1,'获取成功',$pagination->total(),20,$pagination->currentPage(),$paginationArray['data'])
			);
		}
        return '';
	}
	/**
	 * 获取用户基本信息
	 * @api GET /api/1.0/public/msc/user/user-profile
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>post请求字段：</b>
	 * * string        uid        用户ID(必须的)
	 *
	 * @return json {id:用户Id,username:用户名，avatar:头像,user_type:用户类型(student or teacher),user_perfile:用户详细信息({name：姓名，code：学号，qq：QQ，class：班级，grade：年级，professional：专业，student_type：学生类型} or {code:工号，teacher_dept：科室})}
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-09
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
    public function getUserProfile(Request $request){
        $loginInfo=$request->only(['uid']);
		$uid=intval($loginInfo['uid']);
		$user=User::find($uid);

        if(count($user)>0)
        {
            try{
				$personInfo=[];
				if($this->postIsTeacher($user->id))
				{
					$personInfo=Teacher::find($user->id);
					$type='teacher';
				}
				if($this->postIsStudent($user->id))
				{
					$personInfo=Student::find($user->id);
					$type='student';
				}
                if(empty($personInfo))
                {
                    //return response()->json($this->fail(new \Exception('用户信息错误')));
					throw new \Exception('用户信息错误');
                }
				$dataReturn=[
					'id'=>$user->id,
					'username'=>$user->username,
					'avatar'=>$user->avatar,
					'user_perfile'=>$personInfo,
					'usr_type'=>$type
				];
				return response()->json(
					$this->success_data($dataReturn,1,'获取成功')
				);
            }
            catch(\Exception $ex){
                return response()->json($this->fail($ex));
            }
        }
		else
		{
			return response()->json($this->fail(new \Exception('用户信息不存在')));
		}
    }
	/**
	 * 判断用户是否微信认证或登录
	 * @api POST /api/1.0/public/msc/user/login-by-opendid
	 * @access public
	 *
	 * @param Request $request post请求<br><br>
	 * <b>post请求字段：</b>
	 * * string        opendid        微信OpendID(必须的)
	 *
	 * @return json {id:用户Id,username:用户名，avatar:头像,user_type:用户类型(student or teacher),user_perfile:用户详细信息({name：姓名，code：学号，qq：QQ，class：班级，grade：年级，professional：专业，student_type：学生类型} or {code:工号，teacher_dept：科室})}
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function postLoginByOpendid(Request $request){
		$this->validate($request, [
				'opendid'=>'required'
		]);
		$openid=$request->only('opendid');
		if(!empty($openid))
		{
			$userInfo=User::where('openid',$openid)->get();
			$user=$userInfo->first();

			$personInfo=[];
			if($this->postIsTeacher($user->id))
			{
				$personInfo=Teacher::find($user->id);
				$type='teacher';
			}
			if($this->postIsStudent($user->id))
			{
				$personInfo=Student::find($user->id);
				$type='student';
			}
			if(!empty($user))
			{
				$dataReturn=[
						'id'=>$user->id,
						'username'=>$user->username,
						'avatar'=>$user->avatar,
						'user_perfile'=>$personInfo,
						'usr_type'=>$type
				];
				return response()->json(
						$this->success_data($dataReturn,1,'登录成功')
				);
			}
			else
			{
				return response()->json(
						$this->fail(new \Exception('用户不存在'))
				);
			}
		}
        return '';
	}

	/**
	 * 判断用户是否是教师
	 * @access private
	 *
	 * @param
	 * * string        id        用户id(必须的)
	 *
	 * @return booler
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
    private function postIsTeacher($id){
		if($id)
		{
			$teacher=Teacher::find($id);
			if(empty($teacher))
			{
                return $teacher;
			}
			else
			{
                return false;
			}
		}
        return false;
	}

	/**
	 * 判断用户是否为学生
	 * @access private
	 *
     * @param
	 * * string        id        用户id(必须的)
	 *
	 * @return blooer
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-06 15:22
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	private function postIsStudent($id){
		if($id)
		{
			$student=Student::find($id);
			if(empty($student))
			{
				return $student;
			}
			else
			{

				return false;
			}
		}
        return '';
	}

    /**
     * 关联用户ID和微信opendID
     * @api POST /api/1.0/public/msc/user/relative-openid-user
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int        id        用户ID(必须的)
     * * string     openid    微信OpenId(必须的)
     *
     * @return json ['data'=>{'id':用户id,username:用户名,openid：用户修改后的微信openID}]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-06 15:55
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRelativeOpenidUser(Request $request){
        $this->validate($request, [
            'id'=>'required|integer|min:1',
            'openid'=>'required'
        ]);
        $data=$request->only(['id','openid']);
        $id=$data['id'];
        $openid=$data['openid'];
        if($id)
        {
            try{
                $user=User::find($id);
                if(!empty($user))
                {
                    $user->openid=$openid;
                    $user->save();
                    $data=[
                        'id'=>$user->id,
                        'username'=>$user->username,
                        'openid'=>$user->openid,
                    ];
                    return response()->json(
                        $this->success_data($data,1,'关联成功')
                    );
                }
               else
                {
                    return response()->json(
                        $this->fail(new \Exception('用户不存在'))
                    );
                }
            }
            catch(\Exception $ex){
                return response()->json(
                    $this->fail($ex)
                );
            }
        }
        else
        {
            return '';
        }
    }

    /**
     * 绑定用户手机号(初始绑定用户手机号及修改初始密码)
     * @api POST /api/1.0/public/msc/user/relative-mobile
     * @access public
     *
     * @param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     * * int           id          用户ID(必须的)
     * * string        password     用户密码(必须的)
     * * string        mobile       手机号码(必须的)
     * * string        password_confirmation   重复密码(必须的)
     *
     * @return json ['data'=>{'id':用户id,username:用户名,mobile：用户修改后的手机号码}]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-06 16:41
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRelativeMobile(Request $request){
        $this->validate($request, [
            'id'=>'required|integer|min:1',
            'mobile'=>'required|mobile_phone',
            'password'=>'required|confirmed',
        ]);
		$formData=$request->only(['id','mobile','password']);
		$id=(int)$formData['id'];
		$password=e($formData['password']);
		$mobile=e($formData['mobile']);
		DB::beginTransaction();
        try {
			$password = $this->passwordHiiden($password);
			$userInfo=User::find($id);
			$userInfo->password=$password;
			$userInfo->mobile=$mobile;
			if($userInfo->save())
			{
				DB::commit();
				return response()->json(
						$this->success_data($userInfo,1,'绑定成功')
				);
			}
			else
			{
				throw new \Exception('绑定失败');
			}
        }
        catch(\Exception $ex)
        {
			DB::rollback();
            return response()->json(
                $this->fail($ex)
            );
        }
    }
    private function passwordHiiden($password){
        return bcrypt($password);
    }

	/**
	 * 发送注册验证码
	 * @api GET /api/1.0/public/msc/user/reg-moblie-verify
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>post请求字段：</b>
	 * * string        mobile        手机号(必须的)
	 *
	 * @return json {'expiretime':过期时间,'mobile':'手机号'}
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-11 16:40
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getRegMoblieVerify(Request $request){
		$this->validate($request, [
				'mobile' 		=> 	'required|size:11|mobile_phone',
		]);
		$mobile=$request->get('mobile');
		$SysValidatecode=new SysValidatecode();
		try{
			$verify=$SysValidatecode->getMobileRegVerify($mobile);
			$dataReturn=[
					'expiretime'=>$verify->expiretime,
					'mobile'=>$verify->mobile
			];
			//Common::sendSms($verify->mobile,'注册验证码：'.$verify->code);
			return response()->json(
					$this->success_data($dataReturn,1,'短信已发送')
			);
		}
		catch(\Exception $ex)
		{
			return response()->json($this->fail($ex));
		}
	}

	/**
	 * 验证注册用户手机验证码
	 * @api GET /api/1.0/public/msc/user/reg-check-mobile-verfiy
	 * @access public
	 *
	 * @param Request $request get请求<br><br>
	 * <b>get请求字段：</b>
	 * * string        mobile        手机号(必须的)
	 * * string        code        	手机验证码(必须的)
	 *
	 * @return json {''result':'验证结果,成功为true','mobile':'当前手机号'}
	 *
	 * @version 1.0
	 * @author Luohaihua <Luohaihua@misrobot.com>
	 * @date 2015-11-11 16:43
	 * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
	 *
	 */
	public function getRegCheckMobileVerfiy(Request $request){
		$this->validate($request, [
				'mobile' 		=> 	'required|size:11|mobile_phone|unique:users,mobile',
				'code' 		=> 	'required',
		],[
			'mobile.unique'=>'该手机已经注册了',
			'mobile.mobile_phone'=>'请填写正确的手机号',
		]);
		$mobile=$request->get('mobile');
		$code=(int)$request->get('code');
		if(strlen($code)!=6)
		{
			return response()->json(
					$this->fail(new \Exception('验证码不正确'))
			);
		}
		$sysValidatecodeModel=new SysValidatecode();
		$verifyList=$sysValidatecodeModel->where('uid','=',0)
				->where('mobile','=',$mobile)
				->where('expiretime','>=',time())
				->where('type','=',1)->get();
		$codeList=[];
		$codeIndexList=[];
		foreach ($verifyList as $item) {
			$codeList[]=$item->code;
			$codeIndexList[$item->code]=$item;
		}
		if(in_array($code,$codeList))
		{
			$checkCode=$codeIndexList[$code];
			$checkCode->expiretime=time();
			$checkCode->save();
			$dataReturn=[
					'result'=>true,
					'mobile'=>$checkCode->mobile
			];
			return response()->json(
					$this->success_data($dataReturn,1,'验证成功')
			);
		}
		else
		{
			$dataReturn=[
					'result'=>false,
					'mobile'=>$mobile
			];
			return response()->json(
					$this->success_data($dataReturn,1,'验证失败')
			);
		}


	}

	/**
	 * 通过id查询数据返回页面进行展示
	 */
	public function showStudentById(Request $request){
		$id=(int)$request::get('id');
		$Student=User::find($id);
		return view()->with('Student',$Student);

	}

}