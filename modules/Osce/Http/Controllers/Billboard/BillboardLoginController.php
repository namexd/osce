<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/4
 * Time: 17:45
 */

namespace Modules\Osce\Http\Controllers\Billboard;


use Illuminate\Http\Request;
use Modules\Osce\Entities\PadLogin\PadLoginRepository;
use Modules\Osce\Entities\PadLogin\Time;
use Modules\Osce\Http\Controllers\CommonController;

class BillboardLoginController extends CommonController
{
    /**
     *
     * @url
     * @access public
     * @param PadLoginRepository $padLoginRepository
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getIndex(PadLoginRepository $padLoginRepository)
    {
        \App::bind('TimeInterface', function () {
            return new Time();
        });

        $examList = $padLoginRepository->examList(\App::make('TimeInterface'));
        return view('osce::admin.billboard_login', ['exam' => $examList]);
    }

    /**
     *
     * @url
     * @access public
     * @param Request $request
     * @请求字段：
     * 考试id : exam_id
     * @return mixed
     * @version 3.6
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-04
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function postIndex(Request $request)
    {
        //验证
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
            'exam_id' => 'required|integer'
        ], [
            'exam_id.required' => '考试id为必填',
            'exam_id.integer' => '考试id必须为数字',
        ]);
        //获取参数
        $username = $request->input('username');
        $password = $request->input('password');
        try {
            $user = \Auth::attempt(['username' => $username, 'password' => $password]);

            if ($user) {
                return redirect()->route('osce.billboard.getIndex', ['exam_id' => $request->get('exam_id')]);
            } else {
                throw new \Exception('账号密码错误');
            }
        } catch (\Exception $ex) {
            return redirect()->back()->withErrors($ex->getMessage());
        }
    }

}