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
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\WatchLog;
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
     * @url api/1.0  /osce/drawlots/examinee
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

    /**
     * 根据传入的腕表编号和房间id分配应该要去考站(接口)
     * @method POST
     * @url api/1.0  /osce/drawlots/station
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @internal param Request $request
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *                     uid   腕表编号
     *                     room_id  考场id
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-20 15:10
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStation(Request $request)
    {
        try {
            //验证
            $this->validate($request, [
                'uid' => 'required|string',
                'room_id' => 'required|integer'
            ]);

            //获取uid和room_id
            $uid = $request->input('uid');
            $roomId = $request->get('room_id');
            //根据uid来查对应的考生
            $watchLog = WatchLog::where('watch_id',$uid)->first();

            if (!$watchLog) {
                throw new \Exception('没有找到对应的腕表信息！');
            }
            if (!$student = $watchLog ->student) {
                throw new \Exception('没有找到对应的学生信息！');
            }

            //如果考生走错了房间
            if (ExamQueue::where('room_id',$roomId)->where('student_id',$uid)->select('id')->get()->isEmpty()) {
                throw new \Exception('该名学生走错了考场！');
            }
            //使用抽签的方法进行抽签操作
            $result = $this->drawlots($student, $roomId);

            return response()->json($this->success_data($result));

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 抽签的方法
     * @param $student
     * @param $roomId
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     */
    private function drawlots($student, $roomId)
    {
        try {
            //从ExamQueue表中将房间和状态对应的列表查出
            $station = ExamQueue::where('room_id' , '=' , $roomId)
                ->where('status' , '=' , 0)
                ->get();
            //获得已经被选择的考站id对象
            $stationIds = $station->pluck('station_id');
            //将其变成一个一维数组
            $stationIds = $stationIds->all();
            //为该名考生分配一个还没有选择的station_id
            $stationIds = RoomStation::where('room_id',$roomId)
                ->whereNotIn('station_id', $stationIds)
                ->select([
                    'station_id'
                ])
                ->get();

            //随机获取一个考站
            $stationIds = $stationIds->pluck('station_id');
            $ranStationId = $stationIds->random();
            //将这个值保存在队列表中
            if (!$examQueue = ExamQueue::where('student_id',$student->id)->first()) {
                throw new \Exception('在队列中没有找到考生信息');
            };
            $examQueue -> status = 1;
            $examQueue -> station_id = $ranStationId;
            if (!$result = $examQueue -> save()) {
                throw new \Exception('抽签失败！请重试');
            };
            return [$result,$station];
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

}