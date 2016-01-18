<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2016/1/9
 * Time: 13:15
 */

namespace Modules\Osce\Http\Controllers\Wechat;

use App\Entities\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Repositories\Common;
use Auth;

class UserController  extends CommonController
{

    public function getRegister(){
        return view('osce::wechat.user.register');

    }

    /**
     * 学生/老师注册
     * @url GET /osce/admin/user/register
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        mobile       手机号(必须的)
     * * string        password     密码(必须的)
     * * string        repassword   重复密码(必须的)
     * * string        name         姓名(必须的)
     * * string        type         注册类型(必须的)
     * * string        gender       性别
     * * string        nickname     昵称
     * * string        idcard       身份证号(学生必须的)
     *
     * @return Redirect；
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postRegister(Request $request){

        $this   ->validate($request,[
            'mobile'    =>  'required',
            'password'  =>  'required|confirmed',
            'password_confirmation'=>  'required',
            'name'      =>  'required',
            'type'      =>  'required',
            'gender'    =>  'sometimes',
            'nickname'  =>  'sometimes',
            'idcard'    =>  'sometimes',
        ],[
            'mobile.required'       =>  '手机号必填',
            'password.required'     =>  '密码必填',
            'repassword.required'   =>  '重复密码必填',
            'repassword.confirmed'  =>  '两次密码输入不一致',
            'name.required'         =>  '姓名必填',
            'type.required'         =>  '注册类型必选',
        ]);
        $mobile     =   $request    ->  get('mobile');
        $password   =   $request    ->  get('password');
        $type       =   $request    ->  get('type');
        $name       =   $request    ->  get('name');
        $gender     =   $request    ->  get('gender');
        $nickname   =   $request    ->  get('nickname');
        $idcard     =   $request    ->  get('idcard');
        \DB::beginTransaction();
        try
        {
//            dd($mobile,$password,$type,$name,$gender,$nickname,$idcard);
            if($type)
            {
                $idcard =   $request    ->  get('idcard');
                if(empty($idcard))
                {
                    throw new \Exception('学生注册身份证号必填');
                }
            }
            $user   =   User::where('mobile','=',$mobile)->first();
            if($user)
            {
                throw new \Exception('此手机号已经被使用了，请使用你的手机号进行密码找回');
            }
            else
            {
                $user   =   Common::registerUser(['username'=>$mobile],$password);
                $user   ->  name        =   $name;
                $user   ->  gender      =   $gender;
                $user   ->  nickname    =   $nickname;
                if($idcard)
                {
                    $user   ->  name    =   $idcard;
                }
                if($user->save())
                {
                    \DB::commit();
                    return redirect()->route('osce.wechat.user.getLogin');
                }
                else
                {
                    throw new \Exception('注册失败');
                }
            }
        }
        catch (\Exception $ex)
        {
            \DB::rollBack();
            return redirect()->back()->withErrors($ex);
        }

    }

    /**
     * 登录表单
     * @url GET /osce/wechat/user/login
     * @access public
     *
     * @return View
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getLogin(){
        return view('osce::wechat.user.login');
    }

    /**
     *
     * @url GET /osce/admin/user/login
     * @access public
     *
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        username        手机号(必须的)
     * * string        password        密码(必须的)
     *
     * @return Redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postLogin(Request $request){
        $this   ->validate($request,[
            'username'  =>  'required',
            'password'  =>  'required',
        ]);
        $username   =   $request    ->  get('username');
        $password   =   $request    ->  get('password');

        if (Auth::attempt(['username' => $username, 'password' => $password]))
        {
            return redirect()->route('osce.wechat.index.getIndex');
        }
        else
        {
            return redirect()->back()->withErrors('账号密码错误');
        }
    }

    /**
     * 忘记密码 表单
     * @url GET /osce/wechat/user/forget-password
     * @access public
     *
     * @return void
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getForgetPassword(){
        return view('osce::wechat.user.forget_pwd');
    }

    /**
     * 重置密码表单
     * @url GET /osce/admin/invigilator/reset-password
     * @access public
     *
     * <b>get请求字段：</b>
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     * * string        参数英文名        参数中文名(必须的)
     *
     * @return view;
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResetPassword(){
        $user   =   Auth::user();
        if(is_null($user))
        {
            return redirect()->route('osce.wechat.user.getLogin');
        }
        //return view();
    }

    /**
     * 获取重置密码验证
     * @url GET /osce/wechat/user/reset-password-verify
     * @access public
     *
     * @param UserRepository $user
     * @param Request $request
     * <b>get请求字段：</b>
     * * string        mobile        手机号(必须的)
     *
     * @return Json
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-12-29 17:09
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getResetPasswordVerify(UserRepository $user,Request $request)
    {
        $this   ->  validate($request,[
            'mobile'    =>  'required'
        ],[
            'mobile.required'    =>  '请输入手机号'
        ]);
        $mobile =   e($request    ->  get('mobile'));
        try
        {
            $user->getResetPasswordVerify($mobile);
            return response()->json(
                $this->success_data([],1,'发送成功')
            );
        }
        catch(\Exception $ex)
        {
            return response()->json($this   ->fail($ex));
        }
    }

    /**
     * 重置密码处理
     * @url /osce/wechat/user/reset-password
     * @access public
     *
     * @param Request $request
     * <b>post 请求字段：</b>
     * * string        mobile        电话(必须的)
     * * string        verify        验证码(必须的)
     * * string        password      密码(必须的)
     * * string        repassword    重复密码(必须的)
     *
     * @return redirect
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2016-01-10 15:41
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postResetPassword(UserRepository $user,Request $request){
        $this   ->  validate($request,[
            'mobile'    =>  'required',
            'verify'    =>  'required',
            'password'  =>  'required',
            'repassword'=>  'required|confirmed:password',
        ],[
            'mobile.required'    =>  '请输入手机号',
            'verify.required'    =>  '请输入验证码',
            'password.required'  =>  '请输入密码',
            'repassword.required'=>  '请输入确认密码',
            'repassword.confirmed'=>  '两次密码',
        ]);
        $data   =   [
            'mobile'    =>  $request    ->  get('mobile'),
            'code'      =>  $request    ->  get('verify'),
        ];
        $password   =   $request    ->  get('password');
        try{
            if($user->getRegCheckMobileVerfiy($data))
            {
                $password  =   bcrypt($password);
                $user   =   User::where('mobile','=',$password)->first();
                if(empty($user))
                {
                    throw new \Exception('用户不存在');
                }
                $user       ->   password   =   $password;
                if($user    ->  save())
                {
                    return  redirect()      ->  route('osce.wechat.user.getLogin');
                }
            }
            else
            {
                throw new \Exception('验证码错误');
            }
        }
        catch(\Exception $ex)
        {
            return  redirect()  ->  back()  ->  withErrors($ex);
        }
    }


}