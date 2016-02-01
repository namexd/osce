<?php
/**
 * Created by PhpStorm.
 * User: j5110
 * Date: 2016/1/20
 * Time: 10:25
 */

namespace Modules\Osce\Http\Controllers\Api\Pad;


use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Teacher;
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
    private function getRoomId($teacher_id)
    {
        try {
            //通过教师id去寻找对应的考场,返回考场对象
            $room = StationTeacher::where('user_id', $teacher_id)->orderBy('begin_dt','desc')->first()->station->room;

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
     * @url api/1.0/osce/drawlots/examinee
     * @access public
     * @param Request $request
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
    public function getExaminee(Request $request)
    {
        try {
            //首先得到登陆者id
            $id = $request->input('id');

            list($room_id, $station, $stationNum) = $this->getRoomIdAndStation($id);

            //获取正在考试中的考试
            $exam = Exam::where('status',1)->first();

            if (is_null($exam)) {
                throw new \Exception('今天没有正在进行的考试');
            }

            $examId = $exam->id;
            //从队列表中通过考场ID得到对应的考生信息
            $examQueue =  ExamQueue::examineeByRoomId($room_id, $examId, $stationNum);

            if (!$examQueue->isEmpty()) {
                //将老师对应的考站写进对象
                $examQueue->station_name = $station->name;
                $examQueue->station_id = $station->id;
                $examQueue->exam_id = $examId;
            } else {
                throw new \Exception('当前没有符合标准的数据');
            }

//            $examQueue = [
//                0 => ['student_id' => 1,
//                    'student_avator' => 'http://211.149.235.45:9090/mixiong//uploads/20160120/f5cc03fc-a654-4d9b-8a0c-bede8a5d4730.jpg',
//                    'student_code' => '1234',
//                    'student_name' => '测试名字1',
//                    'station_name' => '当前考站1'],
//                1 => ['student_id' => 2,
//                    'student_avator' => 'http://211.149.235.45:9090/mixiong//uploads/20160120/f5cc03fc-a654-4d9b-8a0c-bede8a5d4730.jpg',
//                    'student_code' => '12345',
//                    'student_name' => '测试名字2',
//                    'station_name' => '当前考站2'],
//                2 => ['student_id' => 3,
//                    'student_avator' => 'http://211.149.235.45:9090/mixiong//uploads/20160120/f5cc03fc-a654-4d9b-8a0c-bede8a5d4730.jpg',
//                    'student_code' => '123456',
//                    'student_name' => '测试名字3',
//                    'station_name' => '当前考站3'],
//            ];

            return response()->json($this->success_data($examQueue));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 根据考场ID获取当前时间段的考生列表(接口)
     * @method GET
     * @url api/1.0  /osce/drawlots/next-examinee
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @internal param Request $request
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-23 12:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getNextExaminee(Request $request)
    {
        try {
            $id = $request->input('id');

            list($room_id, $station, $stationNum) = $this->getRoomIdAndStation($id);
            //获取正在考试中的考试
            $exam = Exam::where('status',1)->first();
            $examId = $exam->id;

            $examQueue = ExamQueue::nextExamineeByRoomId($room_id, $examId,$stationNum);
    //        $examQueue = [
    //            0 => ['student_id' => 1,
    //                'student_avator' => 'http://211.149.235.45:9090/mixiong//uploads/20160122/0c0df369-9723-4b39-ae42-722136062b0d.jpg',
    //                'student_code' => '1234',
    //                'student_name' => '测试名字1',
    //                'station_name' => '当前考站1'],
    //            1 => ['student_id' => 2,
    //                'student_avator' => 'http://211.149.235.45:9090/mixiong//uploads/20160122/0c0df369-9723-4b39-ae42-722136062b0d.jpg',
    //                'student_code' => '12345',
    //                'student_name' => '测试名字2',
    //                'station_name' => '当前考站2'],
    //            2 => ['student_id' => 3,
    //                'student_avator' => 'http://211.149.235.45:9090/mixiong//uploads/20160122/0c0df369-9723-4b39-ae42-722136062b0d.jpg',
    //                'student_code' => '123456',
    //                'student_name' => '测试名字3',
    //                'station_name' => '当前考站3'],
    //        ];
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
            $watchLog = ExamScreeningStudent::where('watch_id',$uid)->where('is_end',0)->orderBy('created_at','desc')->first();

            if (!$watchLog) {
                throw new \Exception('没有找到对应的腕表信息！');
            }

            if (!$student = $watchLog ->student) {
                throw new \Exception('没有找到对应的学生信息！');
            }

            $studentId = $watchLog->student_id;
            //如果考生走错了房间
//            dd($studentId,$roomId);
            if (ExamQueue::where('room_id',$roomId)->where('student_id',$studentId)->get()->isEmpty()) {
                throw new \Exception('当前考生走错了考场');
            }

            //使用抽签的方法进行抽签操作
            $result = $this->drawlots($student, $roomId);

            //判断时间
            $this->judgeTime($studentId);

            return response()->json($this->success_data($result));

        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 登陆之后根据老师id返回考站信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @author Jiangzhiheng
     */
    public function getStationList(Request $request)
    {
        try {
            //获取当前登陆者id
            $id = $request->input('id');

            //根据id获取考站信息
            $station = StationTeacher::where('user_id',$id)->first()->station;

            return response()->json($this->success_data($station));
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

            //获得该场考试的exam_id
            if ($station->isEmpty()) {
                throw new \Exception('当前队列中找不到符合的考试');
            }

            $examId = $station->first()->exam_id;

            //判断如果是以考场分组，就抽签
            if (Exam::findOrFail($examId)->sequence_mode == 1) {
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
                    throw new \Exception('没有找到考生信息');
                };
                $examQueue -> status = 1;
                $examQueue -> station_id = $ranStationId;
                if (!$examQueue -> save()) {
                    throw new \Exception('抽签失败！请重试');
                };

                //将考站的信息返回
                return Station::findOrFail($ranStationId);
            } else {
                //如果是以考站分组，直接按计划好的顺序给出
                //查询该学生当前应该在哪个考站考试
                $examQueue = ExamQueue::where('student_id',$student->id)
                    ->where('status',0)
                    ->orderBy('begin_dt','asc')
                    ->get();

                if ($examQueue->isEmpty()) {
                    throw new \Exception('该名考生不在计划中');
                }

                //获得他应该要去的考站id
                $tempObj = $examQueue->first();
                $stationId = $tempObj->station_id;

                //将队列状态变更为1
                $tempObj->status = 1;
                if (!$tempObj->save()) {
                    throw new \Exception('当前抽签失败');
                }

                //查出考站的信息
                return Station::findOrFail($stationId);
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param $user
     * @return array
     * @author Jiangzhiheng
     */
    private function getRoomIdAndStation($id)
    {
        //获取当前老师的考场对象
        $room = $this->getRoomId($id);

        //获得考场的id
        $room_id = $room->id;
        //获得当前考场考站的个数
        $stationNum = RoomStation::where('room_id',$room_id)->get()->count();
        //获得当前老师所在的考站
        $station = StationTeacher::where('user_id',$id)->first()->station;
        return array($room_id, $station, $stationNum);
    }

    /**
     * 判断时间
     * @param $uid
     * @throws \Exception
     * @author Jiangzhiheng
     */
    private function judgeTime($uid)
    {
        //获取当前时间
        $date = date('Y-m-d H:i;s');

        //将当前时间与队列表的时间比较，如果比队列表的时间早，就用队列表的时间，否则就整体延后
        $studentObj = ExamQueue::where('student_id', $uid)->where('status', 1)->first();
        if (!$studentObj) {
            throw new \Exception('当前没有符合条件的队列');
        }
        $studentBeginTime = $studentObj->begin_dt;
        $studentEndTime = $studentObj->end_dt;
        if (strtotime($date) > strtotime($studentBeginTime)) {
            $diff = strtotime($date) - strtotime($studentBeginTime);
            $studentObjs = ExamQueue::where('student_id', $uid)->where('status', '<', 2)->get();
            foreach ($studentObjs as $studentObj) {
                $studentObj->begin_dt = date('Y-m-d H:i:s', strtotime($studentBeginTime) + $diff);
                $studentObj->end_dt = date('Y-m-d H:i:s', strtotime($studentEndTime) + $diff);
                if (!$studentObj->save()) {
                    throw new \Exception('抽签失败！');
                }
            }
        }
    }

}