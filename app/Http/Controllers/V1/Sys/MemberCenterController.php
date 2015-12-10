<?php
/**
 * Created by PhpStorm.
 * User: fengyell <Luohaihua@misrobot.com>
 * Date: 2015/11/10
 * Time: 15:37
 */

namespace App\Http\Controllers\V1\Sys;
use App\Entities\Sys\SysValidatecode;
use App\Entities\Sys\User;
use App\Http\Controllers\V1\ApiBaseController;
use App\Repositories\Common;
use Illuminate\Http\Request;
use DB;

class MemberCenterController extends ApiBaseController
{
    /**
     * 获取用户基本信息
     * @api GET /api/1.0/private/user/membercenter/user-by-id
     * @access public
     *
     * @param Request $request get<br><br>
     * <b>get：</b>
     * * int        id        用户ID(必须的)
     *
     * @return JSON {'id':用户ID,'username':用户名,'avatar':头像,'nickname':昵称,'mobile':手机号,'gender':性别}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getUserById(Request $request){
        $id=$request->get('id');
        $user= User::find($id);
        if($user)
        {
            $dataReturn=[
                'id'=>$user->id,
                'username'=>$user->username,
                'avatar'=>$user->avatar,
                'nickname'=>$user->nickname,
                'mobile'=>$user->mobile,
                'gender'=>$user->gender,
                'idcard'=>$user->idcard
            ];
            return response()->json(
                $this->success_data($dataReturn,1,'获取成功')
            );
        }
    }

    /**
     * 获取用户扩展信息
     * @api GET /api/1.0/private/user/membercenter/user-profile
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        id        用户ID(必须的)
     *
     * @return json {name：姓名，code：学号，qq：QQ，class：班级，grade：年级，professional：专业，student_type：学生类型} or {code:工号，teacher_dept：科室})}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 17:20
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getUserProfile(Request $request){
        $id=$request->get('id');
        $userModel=new User();
        $profileList=$userModel->getUserProfileByIds($id);
        $profile= $profileList[$id]->toArray();

        foreach($profile as $feild=>$value)
        {
            $dataReturn[$feild]=$value;
        }
        return response()->json(
            $this->success_data($dataReturn,1,'获取成功')
        );
    }

    /**
     * 获取用户手机验证码
     * @api GET /api/1.0/private/user/membercenter/mobile-verify
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string        mobile        用户手机号(必须的)
     * * string        uid        用户ID(必须的)
     *
     * @return json {'expiretime':过期时间,'mobile':'手机号'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 15:30
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getMobileVerify(Request $request){
        $this->validate($request, [
            'mobile' 		=> 	'required|size:11|mobile_phone',
            'uid' 		=> 	'required',
        ]);
        $mobile=$request->get('mobile');
        $uid=(int)$request->get('uid');
        $SysValidatecode=new SysValidatecode();
        try{
            $verify=$SysValidatecode->getMobileVerify($mobile,$uid);
            $dataReturn=[
                'expiretime'=>$verify->expiretime,
                'mobile'=>$verify->mobile
            ];
            Common::sendSms($verify->mobile,'绑定验证码：'.$verify->code);
            return response()->json(
                $this->success_data($dataReturn,1,'获取成功')
            );
        }
        catch(\Exception $ex)
        {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 验证手机验证码
     * @api GET /api/1.0/private/user/membercenter/check-mobile-verfiy
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        mobile        手机号(必须的)
     * * string        uid        用户ID(必须的)
     * * string        code        用户提交的验证码(必须的)
     *
     * @return json {''result':'验证结果,成功为true','mobile':'当前手机号'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-11 15：40
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getCheckMobileVerfiy(Request $request){
        $this->validate($request, [
            'mobile' 		=> 	'required|size:11|mobile_phone',
            'uid' 		=> 	'required',
            'code' 		=> 	'required',
        ]);
        $mobile=$request->get('mobile');
        $uid=(int)$request->get('uid');
        $code=(int)$request->get('code');
        $sysValidatecodeModel=new SysValidatecode();
        $verifyList=$sysValidatecodeModel->where('uid','=',$uid)
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
    public function postImportUser(Request $request){
        Common::getExclData($request,'excl');
    }

    /**
     *
     * @api GET /api/1.0/private/user/membercenter/mobile
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * int        uid        用户ID(必须的)
     *
     * @return json {"uid":用户ID,"mobile":"当前绑定的手机号"}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-17 11:19
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getMobile(Request $request){
        $this->validate($request, [
            'uid' 		=> 	'required|integer|min:0',
        ]);
        $uid=$request->get('uid');
        $user=User::find($uid);
        if(!empty($user))
        {
            $dataReturn=[
                'uid'=>$user->id,
                'mobile'=>$user->mobile
            ];
            return response()->json(
                $this->success_data($dataReturn,1,'获取成功')
            );
        }
        else
        {
            return response()->json($this->fail(new \Exception('没有该用户')));
        }
    }

    /**
     * 验证并绑定手机
     * @api POST /api/1.0/private/user/membercenter/reletive-mobile
     * @access public
     *
     * @param Request $request get请求<br><br>
     * <b>post请求字段：</b>
     * * string        mobile        手机号(必须的)
     * * string        uid        用户ID(必须的)
     * * string        code        用户提交的验证码(必须的)
     *
     * @return json {''result':'验证结果,成功为true','mobile':'当前手机号'}
     *
     * @version 1.0
     * @author Luohaihua <Luohaihua@misrobot.com>
     * @date 2015-11-17 13:38
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function postReletiveMobile(Request $request){
        $this->validate($request, [
            'mobile' 		=> 	'required|size:11|mobile_phone',
            'uid' 		=> 	'required',
            'code' 		=> 	'required',
        ]);
        $mobile=$request->get('mobile');
        $uid=(int)$request->get('uid');
        $code=(int)$request->get('code');
        DB::beginTransaction();
        $sysValidatecodeModel=new SysValidatecode();
        $verifyList=$sysValidatecodeModel->where('uid','=',$uid)
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
            $user=User::find($uid);
            $user->username=$checkCode->mobile;
            $user->mobile=$checkCode->mobile;
            if($user->save())
            {
                DB::commit();
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
                DB::rollback();
                return response()->json($this->fail(new \Exception('绑定失败')));
            }
        }
        else
        {
            $dataReturn=[
                'result'=>false,
                'mobile'=>$mobile
            ];
            DB::rollback();
            return response()->json(
                $this->success_data($dataReturn,1,'验证失败')
            );
        }
    }
}