<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/5/3
 * Time: 13:50
 */

namespace Modules\Osce\Http\Controllers\Api;


use Illuminate\Http\Request;
use Modules\Osce\Entities\PadLogin\PadLogin;
use Modules\Osce\Entities\PadLogin\PadLoginRepository;
use Modules\Osce\Entities\PadLogin\Screen;
use Modules\Osce\Entities\PadLogin\Time;
use Modules\Osce\Http\Controllers\CommonController;

class LoginPullDownController extends CommonController
{
    public function __construct()
    {
        \App::bind('PadLogin', function () {
            return new PadLogin();
        });

        \App::bind('PadLoginRepository', function () {
            return new PadLoginRepository(\App::make('PadLogin'));
        });
    }

    /**
     * 获取考试下拉菜单
     * @url osce/api/exam-list
     * @access public
     * @param PadLoginRepository $padLogin
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time 2016-05-03
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExamList(PadLoginRepository $padLogin)
    {
        try {
            \App::bind('TimeInterface', function () {
                return new Time();
            });

            $examList = $padLogin->examList(\App::make('TimeInterface'));
            return response()->json($this->success_data($examList));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 获取room下拉菜单
     * @url osce/api/room-list
     * @access public
     * @param Request $request
     * @param PadLoginRepository $padLogin
     * 请求字段：考试id： exam_id
     * @return mixed
     * @version
     * @author JiangZhiheng <JiangZhiheng@misrobot.com>
     * @time
     * @copyright 2013-2016 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getRoomList(Request $request, PadLoginRepository $padLogin)
    {
        $this->validate($request,
            [
                'exam_id' => 'required|integer'
            ]);

        try {
            $data = $padLogin->roomList($request->input('exam_id'));

            //将数据处理完毕后给android
            return response()->json($this->success_data($data));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}