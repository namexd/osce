<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/19
 * Time: 17:23
 */

namespace App\Repositories;
use App\Repositories\BaseRepository;
use Modules\Msc\Entities\Grade;
use Modules\Msc\Entities\StdClass;
use Modules\Msc\Entities\StdProfessional;
use Modules\Msc\Entities\StudentType;
use Modules\Msc\Entities\TeacherDept;
use App\Http\Controllers\V1\ApiBaseController;
use App\Entities\User;
use Modules\Msc\Entities\Student;
use Modules\Msc\Entities\Teacher;
use App\Entities\SysValidatecode;
use Log;
use DB;

class UserRepository extends BaseRepository
{
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
     *
     *
     * @return 用户ID
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-19 17:32
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function regStudent($data){
        try{
            DB::beginTransaction();
            $form_user=$data;
            $form_user['username']=$data['mobile'];
            $form_user['openid']=$data['openid'];
            $form_user['password']=bcrypt($data['password']);
            $user=User::create($form_user->all());
            $form_student=[
                'name'=>$data['name'],
                'class'=>$data['class'],
                'grade'=>$data['grade'],
                'professional'=>$data['professional'],
                'code'=>$data['code'],
                'student_type'=>$data['student_type'],
            ];

            $form_student['id']=$user->id;
            $student=Student::create($form_student);
            DB::commit();
            return $user->id;
        }
        catch(\Exception $ex){
            DB::rollback();
            throw $ex;
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
    public function regTeacher($data){
        try{
            DB::beginTransaction();
            $form_user=[
                'name'=>$data['name'],
                'mobile'=>$data['mobile'],
                'password'=>$data['password'],
                'gender'=>$data['gender'],
                'openid'=>$data['openid']
            ];
            $form_user['username']=$data['mobile'];
            $form_user['password']=bcrypt($data['password']);
            $user=User::create($form_user);

            $form_teacher=[
                'name'=>$data['name'],
                'teacher_dept'=>$data['professional'],
                'code'=>$data['code'],
                'validated'=>1,
                'professionalTitle'=>$data['professionalTitle'],
            ];
            $form_teacher['id']=$user->id;
            Teacher::create($form_teacher);
            DB::commit();
            return $user->id;
        }
        catch(\Exception $ex){
            DB::rollback();
            throw $ex;
        }
    }
    /**
     * 获取班级列表
     * @access public
     *
     * @param
     * * string		keyword			班级名(必须的)
     * * int		page			页码(必须的)
     *
     * @return pagination对象 :['data'=>[{'id':'班级id','code':'班级编号','name':'班级编号'},{……}]]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-06 11:00
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getClassList($keyword=''){
        if(strlen($keyword)>0)
        {
            $pagination=$StdClass=StdClass::where('name','like','%'.$keyword.'%')->paginate(20);
            return $pagination;
        }
        return [];
    }
    /**
     * 获取学生类型列表
     * @access public
     *
     * @return Array [{id:1,name:本科生},{id:2,name:类别名}]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-19 17:43
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getStudentTypeList(){
        $StudentType=New StudentType();
        $list=$StudentType->studentType;
        return $list;
    }
    /**
     * 获取年级列表
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
        return $Grade->getGradeList();
    }
    /**
     * 获取专业列表
     * @access public
     *
     * @param
     * * string        keyword        关键字(必须的)
     * * string        page        		页码(必须的)
     *
     * @return pagination 对象 :['data'=>[{'id':'专业id','code':'专业编号','name':'专业名称'},{……}]]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-06 12:36
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getProfessionalList($keyword){
        if(strlen($keyword)>0)
        {
            return $StdClass=StdProfessional::where('name','like','%'.$keyword.'%')->paginate(20);
        }
        return '';
    }
    /**
     * 获取科室列表
     * @access public
     * @param
     * * string        keyword        关键字(必须的)
     * * string        page        		页码(必须的)
     *
     * @return pagination 对象 :['data'=>[{'id':'科室id','code':'科室编号','name':'科室名称'},{……}]]
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-06 12:42
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getTeacherDeptList($keyword){
        if(strlen($keyword)>0)
        {
            return $StdClass=TeacherDept::where('name','like','%'.$keyword.'%')->paginate(20);

        }
        return '';
    }
    /**
     * 获取用户基本信息
     * @access public
     * @param
     * * int        uid        用户ID(必须的)
     *
     * @return Array {id:用户Id,username:用户名，avatar:头像,user_type:用户类型(student or teacher),user_perfile:用户详细信息({name：姓名，code：学号，qq：QQ，class：班级，grade：年级，professional：专业，student_type：学生类型} or {code:工号，teacher_dept：科室})}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getUserProfile($uid){
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
                    throw new \Exception('用户信息错误');
                }
                $dataReturn=[
                    'id'=>$user->id,
                    'username'=>$user->username,
                    'avatar'=>$user->avatar,
                    'user_perfile'=>$personInfo,
                    'usr_type'=>$type
                ];
                return $dataReturn;
            }
            catch(\Exception $ex){
                throw $ex;
            }
        }
        else
        {
            throw new \Exception('用户信息不存在');
        }
    }
    /**
     * 判断用户是否微信认证或登录
     * @access public
     *
     * @param
     * * string        uid        用户ID(必须的)
     *
     * @return json {id:用户Id,username:用户名，avatar:头像,user_type:用户类型(student or teacher),user_perfile:用户详细信息({name：姓名，code：学号，qq：QQ，class：班级，grade：年级，professional：专业，student_type：学生类型} or {code:工号，teacher_dept：科室})}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function checkOpendid($uid){
        if(!empty($uid))
        {
            $user=User::find($uid);
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
                return $dataReturn;
            }
            else
            {
                throw new \Exception('用户不存在');
            }
        }
        return false;
    }
    /**
     * 关联用户ID和微信opendID
     * @access public
     *
     * @param
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
    public function relativeOpenidUser($data){
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
                    return $data;
                }
                else
                {
                    throw new \Exception('用户不存在');
                }
            }
            catch(\Exception $ex){
                throw $ex;
            }
        }
        else
        {
            throw new \Exception('用户ID必须');
        }
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

    public function relativeMobile($formData){
        $id=(int)$formData['id'];
        $password=e($formData['password']);
        $mobile=e($formData['mobile']);
        DB::beginTransaction();
        try {
            $password = bcrypt($password);
            $userInfo=User::find($id);
            $userInfo->password=$password;
            $userInfo->username=$mobile;
            $userInfo->mobile=$mobile;
            $userInfo->save();

            if($userInfo)
            {
                DB::commit();
                return $userInfo;
            }
            else
            {
                throw new \Exception('绑定失败');
            }
        }
        catch(\Exception $ex)
        {

            DB::rollback();
            throw new $ex;
        }
    }
    /**
     * 发送注册验证码
     * @access public
     *
     * @param
     * * string        mobile        手机号(必须的)
     *
     * @return Array {'expiretime':过期时间,'mobile':'手机号'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 16:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getRegMoblieVerify($mobile){
        $SysValidatecode=new SysValidatecode();
        try{
            $verify=$SysValidatecode->getMobileRegVerify($mobile);
            $dataReturn=[
                'expiretime'=>$verify->expiretime,
                'mobile'=>$verify->mobile
            ];
            Common::sendSms($verify->mobile,'注册验证码：'.$verify->code.' 【敏行医学】');

            return $dataReturn;
        }
        catch(\Exception $ex)
        {
            throw new $ex;
        }
    }

    /**
     * 发送找回密码验证码
     * @access public
     *
     * @param
     * * string        mobile        手机号(必须的)
     *
     * @return Array {'expiretime':过期时间,'mobile':'手机号'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 16:40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResetPasswordVerify($mobile){
        $SysValidatecode=new SysValidatecode();
        try{
            $verify=$SysValidatecode->getMobileResetPasswordVerify($mobile);
            $dataReturn=[
                'expiretime'=>$verify->expiretime,
                'mobile'=>$verify->mobile
            ];
            Common::sendSms($verify->mobile,'你正在重置密码，验证码为：'.$verify->code.' 【敏行医学】');
            return $dataReturn;
        }
        catch(\Exception $ex)
        {
            throw new $ex;
        }
    }

    /**
     * 验证注册用户手机验证码
     * @access public
     *
     * @param
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
    public function getRegCheckMobileVerfiy($formData){
        $mobile=$formData['mobile'];
        $code=$formData['code'];
        if(strlen($code)!=6)
        {
            return response()->json(
                $this->fail(new \Exception('验证码不正确'))
            );
        }
        $sysValidatecodeModel=new SysValidatecode();
        $verifyList=$sysValidatecodeModel->where('uid','=',0)
            ->where('mobile','=',$mobile)
            ->where('expiretime','>',time())
            ->where('type','=',2)->get();
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
            return $dataReturn;
        }
        else
        {
            return false;
        }
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
                return false;
            }
            else
            {
                return $teacher;
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
                return false;
            }
            else
            {
                return $student;
            }
        }
        return '';
    }
}