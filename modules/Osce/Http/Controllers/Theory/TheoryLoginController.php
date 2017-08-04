<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 17:45
 */

namespace Modules\Osce\Http\Controllers\Theory;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestLog;
use Modules\Osce\Entities\TestRecord;
use Pingpong\Modules\Routing\Controller;

class TheoryLoginController extends Controller
{
    /**
     * 登陆的着陆页
     * @url osce/billboard-login/index
     * @access public
     * @param PadLoginRepository $padLoginRepository
     * @return mixed
     * @version 3.6
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-04
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function getIndex()
    {
        if(\Auth::check()){
            $test = TestLog::where('start','<',date('Y-m-d H:i:s'))->where('end','>',date('Y-m-d H:i:s'))->first();
            if($test){
                $userid = \Auth::user()->id;
                $isExist = Student::where('exam_id', $test->exam_id)->where('user_id', $userid)->first();
                if (empty($isExist)) {
                    \Auth::logout();
                    return redirect()->back()->withErrors('你不属于当前考试');
                }
                $isAnswer = TestRecord::where(['logid' => $test->id, 'stuid' => $userid])->first();
                if (!empty($isAnswer)) {
                    \Auth::logout();
                    return redirect()->back()->withErrors('你已经参加过当前考试');
                }
                session(['enterTime' => date('Y-m-d H:i:s')]);
                return redirect()->route('osce.cexam.examinfo', ['testlog_id' => $test->id]);

            } else {
                \Auth::logout();
                return redirect()->back()->withErrors('当前时间没有考试');
            }
        }
        return view('osce::theory.theory_login');
    }

    /**
     * 登陆的着陆处理逻辑
     * @url osce/billboard-login/index
     * @access public
     * @param Request $request
     * @请求字段：
     * 考试id : exam_id
     * @return mixed
     * @version 3.6
     * @author ZouYuChao <ZouYuChao@sulida.com>
     * @time 2016-05-04
     * @copyright 2013-2017 sulida.com Inc. All Rights Reserved
     */
    public function postIndex(Request $request)
    {
        //验证
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);
        //获取参数
        $username = $request->input('username');
        $password = $request->input('password');
        try {
            $test = TestLog::where('start','<',date('Y-m-d H:i:s'))->where('end','>',date('Y-m-d H:i:s'))->first();
            if(!empty($test)){
                $user = \Auth::attempt(['username' => $username, 'password' => $password]);
                //dd($user);
                if ($user) {
                    $userid = \Auth::user()->id;
                    $isExist = Student::where('exam_id',$test->exam_id)->where('user_id',$userid)->first();
                    if(empty($isExist)){
                        \Auth::logout();
                        return redirect()->back()->withErrors('你不属于当前考试');
                    }
                    $isAnswer =  TestRecord::where(['logid'=>$test->id,'stuid'=>$userid])->first();
                    if(!empty($isAnswer)){
                        \Auth::logout();
                        return redirect()->back()->withErrors('你已经参加过当前考试');
                    }
                    session(['enterTime' => date('Y-m-d H:i:s')]);
                    return redirect()->route('osce.cexam.examinfo', ['testlog_id' => $test->id]);
                } else {
                    throw new \Exception('账号密码错误');
                }
            }else{
                return redirect()->back()->withErrors('当前时间没有考试');
            }

        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

}