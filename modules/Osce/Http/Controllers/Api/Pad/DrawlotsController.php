<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/20
 * Time: 10:25
 */

namespace Modules\Osce\Http\Controllers\Api\Pad;


use Illuminate\Http\Request;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;

class DrawlotsController extends CommonController
{
    /**
     *根据老师的id获取对应的考场(接口)
     * @method GET
     * @url /osce/drawlots/room-id
     * @access public
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-20 12:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private function getRoomId()
    {
        try {
            //首先得到登陆者信息
            $user = Auth::user();

            //获取登陆者id，也就是教师id
            $teacher_id = $user->id;
            //通过教师id去寻找对应的考场,返回考场对象
            $room = StationTeacher::where('user_id', $teacher_id)->first()->station->room;
            if ($room->isEmpty()) {
                throw new \Exception('未能查到该老师对应的考场！');
            }
            return $room->first();
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 根据考场ID获取当前时间段的考生列表(接口)
     * @method GET
     * @url api/1.0/private/osce/pad/wait-room
     * @access public
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @internal param Request $request
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-20 12:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExaminee()
    {
        try {
            //获取当前老师的考场对象
            $room = $this->getRoomId();

            //获得考场的id
            $room_id = $room->id;

            //从队列表中通过考场ID得到对应的考生信息
            $examQueue =  ExamQueue::examineeByRoomId($room_id);
            return response()->json($this->success_data($examQueue));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
}