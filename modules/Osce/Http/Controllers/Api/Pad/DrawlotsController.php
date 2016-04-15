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
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\ExamFlowRoom;
use Modules\Osce\Entities\ExamFlowStation;
use Modules\Osce\Entities\ExamMidway\Drawlots;
use Modules\Osce\Entities\ExamMidway\Examinee;
use Modules\Osce\Entities\ExamMidway\RoomMode;
use Modules\Osce\Entities\ExamMidway\StationMode;
use Modules\Osce\Entities\ExamMidway\DrawRoomMode;
use Modules\Osce\Entities\ExamMidway\DrawStationMode;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Room;
use Modules\Osce\Entities\RoomStation;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationTeacher;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\Api\InvigilatePadController;
use Modules\Osce\Http\Controllers\Api\StudentWatchController;
use Modules\Osce\Http\Controllers\CommonController;
use Auth;
use DB;
use Modules\Osce\Repositories\Common;
use Illuminate\Support\Facades\Redis;

class DrawlotsController extends CommonController
{
    /**
     *根据老师的id获取对应的考场(接口)
     * @method GET
     * @url /osce/drawlots/room-id
     * @access public
     * @param $teacher_id
     * @param $examId
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * <b>post请求字段：</b>
     *
     * @version 1.0
     * @author Jiangzhiheng <Jiangzhiheng@misrobot.com>
     * @date 2016-01-20 12:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    private function getRoomId($teacher_id, $examId)
    {
        try {
            //通过教师id去寻找对应的考场,返回考场对象
            $room = StationTeacher::where('user_id', $teacher_id)->where('exam_id', $examId)->orderBy('created_at',
                'desc')->first()->station->room;

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
     * @url osce/pad/examinee
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
        $this->validate($request, [
            'id' => 'required|integer',
            'exam_id' => 'sometimes|integer'
        ]);

        try {
            //首先得到登陆者id
            $id = $request->input('id');
            $examId = $request->input('exam_id', null);
            $redis = Redis::connection('message');
            //获取正在考试中的考试
            $exam = Exam::doingExam($examId);
            if (is_null($exam)) {
                $redis->publish('pad_message', json_encode($this->success_data([], -50, '今天没有正在进行的考试')));
                throw new \Exception('今天没有正在进行的考试', -50);
            } elseif ($exam->status != 1) {
                $redis->publish('pad_message', json_encode($this->success_data([], -777, '当前考试没有进行')));
                throw new \Exception('当前考试没有进行', -777);
            }

            $station = StationTeacher::where('exam_id', '=', $exam->id)
                ->where('user_id', '=', $id)
                ->first();
            if (is_null($station)) {
               $redis->publish('pad_message', json_encode($this->success_data([], 7100, '你没有参加此次考试')));
                throw new \Exception('你没有参加此次考试');
            }

            list($room_id, $stations) = $this->getRoomIdAndStation($id, $exam);
            if ($exam->sequence_mode == 1) {
                $examQueue = ExamQueue::examineeByRoomId($room_id, $exam->id, $stations);
            } elseif ($exam->sequence_mode == 2) {
                $examQueue = ExamQueue::examineeByStationId($station->station_id, $exam->id);
            } else {
                $redis->publish('pad_message', json_encode($this->success_data([], -703, '考试模式不存在')));
                throw new \Exception('考试模式不存在！', -703);
            }


//            $examinee = new Examinee($exam, ['id' => $id]);
//            switch ($exam->sequence_mode) {
//                case 1:
//                    $examinee->setMode(new RoomMode($id, $exam));
//                    $students = $examinee->examinee();
//                    break;
//                case 2:
//                    $examinee->setMode(new StationMode($id, $exam));
//                    $students = $examinee->examinee();
//                    break;
//                default:
//                    throw new \Exception('当前没有这种考试模式！');
//                    break;
//            }
//            foreach ($students as $student) {
//                unset($student['blocking']);
//            }

            $redis->publish('pad_message', json_encode($this->success_data($examQueue,103,'获取成功')));//信息推送
            return response()->json($this->success_data($examQueue));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 根据考场ID获取当前时间段的考生列表(接口)推送
     * @method GET
     * @url osce/pad/examinee
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @internal param Request $request
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *
     * @version 1.0
     * @author wt <Jiangzhiheng@misrobot.com>
     * @date 2016-01-20 12:01
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getExaminee_arr(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'exam_id' => 'sometimes|integer'
        ]);

        try {
            //首先得到登陆者id
            $id = $request->input('id');
            $examId = $request->input('exam_id', null);
            $redis = Redis::connection('message');
            //获取正在考试中的考试
            $exam = Exam::doingExam($examId);
            if (is_null($exam)) {
                $redis->publish('pad_message', json_encode($this->success_data([], -50, '今天没有正在进行的考试')));
                throw new \Exception('今天没有正在进行的考试', -50);
            } elseif ($exam->status != 1) {
                $redis->publish('pad_message', json_encode($this->success_data([], -777, '当前考试没有进行')));
                throw new \Exception('当前考试没有进行', -777);
            }

            $station = StationTeacher::where('exam_id', '=', $exam->id)
                ->where('user_id', '=', $id)
                ->first();
            if (is_null($station)) {
                $redis->publish('pad_message', json_encode($this->success_data([], 7100, '你没有参加此次考试')));
                throw new \Exception('你没有参加此次考试');
            }

            list($room_id, $stations) = $this->getRoomIdAndStation($id, $exam);
            if ($exam->sequence_mode == 1) {
                $examQueue = ExamQueue::examineeByRoomId($room_id, $exam->id, $stations);
            } elseif ($exam->sequence_mode == 2) {
                $examQueue = ExamQueue::examineeByStationId($station->station_id, $exam->id);
            } else {
                $redis->publish('pad_message', json_encode($this->success_data([], -703, '考试模式不存在')));
                throw new \Exception('考试模式不存在！', -703);
            }

            $redis->publish('pad_message', json_encode($this->success_data($examQueue,103,'获取成功')));//信息推送
        } catch (\Exception $ex) {
            return $ex;
        }
    }


    /**
     * 根据考场ID获取当前时间段的考生列表(接口)
     * @method GET
     * @url osce/pad/next-examinee
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
        $this->validate($request, [
            'id' => 'required|integer',
            'exam_id' => 'sometimes|integer'
        ]);

        try {
            $redis = Redis::connection('message');
            $id = $request->input('id');
            $examId = $request->input('exam_id', null);
            //获取正在考试中的考试
            $exam = Exam::doingExam($examId);
            if (is_null($exam)) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3000, '当前没有正在进行的考试')));
                throw new \Exception('当前没有正在进行的考试', 3000);
            }

//            $examinee = new Examinee($exam, ['id' => $id]);
//            switch ($exam->sequence_mode) {
//                case 1:
//                    $examinee->setMode(new RoomMode($id, $exam));
//                    $students = $examinee->nextExaminee();
//                    break;
//                case 2:
//                    $examinee->setMode(new StationMode($id, $exam));
//                    $students = $examinee->nextExaminee();
//                    break;
//                default:
//                    throw new \Exception('当前没有这种考试模式！');
//                    break;
//            }

//            //获取当前老师对应的考站id
            $station = StationTeacher::where('exam_id', '=', $exam->id)
                ->where('user_id', '=', $id)
                ->first();

            if (is_null($station)) {
                $redis->publish('pad_message', json_encode($this->success_data([], -999, '你没有参加此次考试')));
                throw new \Exception('你没有参加此次考试');
            }

            list($room_id, $stations) = $this->getRoomIdAndStation($id, $exam);

            if ($exam->sequence_mode == 1) {
                $examQueue = ExamQueue::nextExamineeByRoomId($room_id, $exam->id, $stations);
            } elseif ($exam->sequence_mode == 2) {

                $examQueue = ExamQueue::nextExamineeByStationId($station->station_id, $exam->id);
            } else {
                $redis->publish('pad_message', json_encode($this->success_data([], -703, '考试模式不存在')));
                throw new \Exception('考试模式不存在！', -703);
            }
           // dd($examQueue);
            //从集合中移除blocking
//            $students->forget('blocking');
            $redis->publish('pad_message', json_encode($this->success_data($examQueue,104,'获取成功')));//信息推送
            return response()->json($this->success_data($examQueue));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }
    /**
     * 根据考场ID获取当前时间段的考生列表(接口)
     * @method GET
     * @url osce/pad/next-examinee
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
    public function getNextExaminee_arr(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer',
            'exam_id' => 'sometimes|integer'
        ]);

        try {
            $redis = Redis::connection('message');
            $id = $request->input('id');
            $examId = $request->input('exam_id', null);
            //获取正在考试中的考试
            $exam = Exam::doingExam($examId);
            if (is_null($exam)) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3000, '当前没有正在进行的考试')));
                throw new \Exception('当前没有正在进行的考试', 3000);
            }



//            //获取当前老师对应的考站id
            $station = StationTeacher::where('exam_id', '=', $exam->id)
                ->where('user_id', '=', $id)
                ->first();

            if (is_null($station)) {
                $redis->publish('pad_message', json_encode($this->success_data([], -999, '你没有参加此次考试')));
                throw new \Exception('你没有参加此次考试');
            }

            list($room_id, $stations) = $this->getRoomIdAndStation($id, $exam);

            if ($exam->sequence_mode == 1) {
                $examQueue = ExamQueue::nextExamineeByRoomId($room_id, $exam->id, $stations);
            } elseif ($exam->sequence_mode == 2) {

                $examQueue = ExamQueue::nextExamineeByStationId($station->station_id, $exam->id);
            } else {
                $redis->publish('pad_message', json_encode($this->success_data([], -703, '考试模式不存在')));
                throw new \Exception('考试模式不存在！', -703);
            }
            // dd($examQueue);
            //从集合中移除blocking
//            $students->forget('blocking');
            $redis->publish('pad_message', json_encode($this->success_data($examQueue,104,'获取成功')));//信息推送
           // return response()->json($this->success_data($examQueue));
        } catch (\Exception $ex) {
            return [];
        }
    }
    /**
     * 获取下个考生信息返回当前组学生信息
     * @method GET
     * @url osce/pad/next-student
     * @access public
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse ${response}
     *
     * @internal param Request $request
     * @internal param Request $request post请求<br><br>
     * <b>post请求字段：</b>
     *
     * @version 1.0
     * @author wt <wangtao@misrobot.com>
     * @date 2016-04-12 12:06
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function nextStudent(Request $request){
        
        $this->validate($request, [
            'exam_queue_id' => 'sometimes|integer',
            'station_id' => 'required|integer',
            'teacher_id' =>'required|integer'

        ], [
            'exam_queue_id.required' => '考生队列编号信息必须',
            'station_id.required' => '考站编号信息必须',
            'teacher_id.required'=>'老师编号信息必须',
        ]);
        try {
            $stationId = (int)$request->input('station_id');
            $examQueueId = (int)$request->input('exam_queue_id');//队列id
            $teacher_id =(int)$request->input('teacher_id');
            if($examQueueId) {
                ExamQueue::where('id', $examQueueId)->increment('next_num', 1);//下一次次数增加
            }
            $exam = Exam::doingExam();
            //$studentModel = new  Student();

            //$studentData = $studentModel->nextStudentList($stationId, $exam);
            list($room_id, $stations) = $this->getRoomIdAndStation($teacher_id, $exam);
            if ($exam->sequence_mode == 1) {
                $examQueue = ExamQueue::examineeByRoomId($room_id, $exam->id, $stations);
            } elseif ($exam->sequence_mode == 2) {
                $examQueue = ExamQueue::examineeByStationId($stationId, $exam->id);
            } else {
                throw new \Exception('考试模式不存在！', -703);
            }
            $request['id']=$teacher_id;
            $request['exam_id']=$exam->id;
            dd(1);
            $this->getNextExaminee_arr($request);//推送下一组
            $this->getExaminee_arr($request);//推送当前小组
            return response()->json(
                $this->success_data($examQueue, 1, '验证完成')
            );

        } catch (\Exception $ex) {
            return response()->json(
                $this->fail($ex)
            );
        }


    }

    /**
     * 根据传入的腕表编号和房间id分配应该要去考站(接口)
     * @method POST
     * @url /osce/pad/station
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
        $connection = \DB::connection('osce_mis');
        $connection->beginTransaction();
        //验证
        $this->validate($request, [
            'uid' => 'required|string',
            'room_id' => 'required|integer',
            'teacher_id' => 'required|integer'
        ]);
        try {
            $examId = $request->input('exam_id', null);
            //获取uid和room_id
            $uid = $request->input('uid');
            $roomId = $request->input('room_id');
            $teacherId = $request->input('teacher_id');
            $redis = Redis::connection('message');
            //根据uid查到对应的腕表编号
            $watch = Watch::where('code', $uid)->first();
            if (is_null($watch)) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3100, '没有找到对应的腕表信息!')));
                throw new \Exception('没有找到对应的腕表信息！', 3100);
            }

            //获取腕表记录实例
            $watchLog = ExamScreeningStudent::where('watch_id', $watch->id)->where('is_end', 0)->orderBy('created_at',
                'desc')->first();
            if (!$watchLog) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3200, '没有找到学生对应的腕表信息!')));
                throw new \Exception('没有找到学生对应的腕表信息！', 3200);
            }

            //获取腕表对应的学生实例
            if (!$student = $watchLog->student) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3300, '没有找到对应的学生信息!')));
                throw new \Exception('没有找到对应的学生信息！', 3300);
            }

//            //判断当前学生是否在当前小组中
            $exam = Exam::doingExam($examId);
            if (is_null($exam)) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3000, '当前没有正在进行的考试!')));
                throw new \Exception('当前没有正在进行的考试', 3000);
            }
            $examId = $exam->id;
            list($room_id, $stations) = $this->getRoomIdAndStation($teacherId, $exam);

            //获取当前老师对应的考站id
            $station = StationTeacher::where('exam_id', '=', $exam->id)
                ->where('user_id', '=', $teacherId)
                ->first();
            if (is_null($station)) {
                $redis->publish('pad_message', json_encode($this->success_data([], 7100, '你没有参加此次考试!')));
                throw new \Exception('你没有参加此次考试', 7100);
            }
            /*
             * 判断当前考生是否是在当前的学生组中
             */
            if ($exam->sequence_mode == 1) {
                //从队列表中通过考场ID得到对应的当前组的考生信息
                $examQueue = ExamQueue::examineeByRoomId($room_id, $examId, $stations);
                if (!in_array($watchLog->student_id, $examQueue->pluck('student_id')->toArray())) {
                    $redis->publish('pad_message', json_encode($this->success_data([], 7200, '该考生不在当前考生小组中!')));
                    throw new \Exception('该考生不在当前考生小组中', 7200);
                }
            } elseif ($exam->sequence_mode == 2) {
                $examQueue = ExamQueue::examineeByStationId($station->station_id, $examId);
                if (!in_array($watchLog->student_id, $examQueue->pluck('student_id')->toArray())) {
                    $redis->publish('pad_message', json_encode($this->success_data([], 7201, '该考生不在当前考生小组中!')));
                    throw new \Exception('该考生不在当前考生小组中', 7201);
                }
            } else {
                $redis->publish('pad_message', json_encode($this->success_data([], -705, '没有这种考试模式!')));
                throw new \Exception('没有这种考试模式！', -705);
            }

            //如果考生走错了房间
            if (ExamQueue::where('room_id', '=', $roomId)
                ->where('student_id', '=', $watchLog->student_id)
                ->where('exam_id', '=', $examId)->get()
                ->isEmpty()
            ) {
                $redis->publish('pad_message', json_encode($this->success_data([], 3400, '当前考生走错了考场!')));
                throw new \Exception('当前考生走错了考场！', 3400);
            }

            //使用抽签的方法进行抽签操作
            $result = $this->drawlots($student, $roomId, $teacherId, $exam);
//            $model = new Drawlots($student, $teacherId, $exam, $roomId);
//            switch ($exam->sequence_mode) {
//                case 1:
//                    $model->mode(new DrawRoomMode());
//                    $result = $model->drawlots();
//                    break;
//                case 2:
//                    $model->mode(new DrawStationMode());
//                    $result = $model->drawlots();
//                    break;
//                default:
//                    throw new \Exception('当前没有这种考试模式');
//                    break;
//            }

            //判断时间
            $this->judgeTime($watchLog->student_id);
            $connection->commit();
            $redis->publish('pad_message', json_encode($this->success_data($result, 1, '抽签成功!')));
            //推送当前学生
            $request['station_id']=$result->id;
            $request['teacher_id']=$teacherId;
            $inv=new InvigilatePadController();
            $inv->getAuthentication_arr($request);//当前考生推送
            return response()->json($this->success_data($result));

        } catch (\Exception $ex) {
            $connection->rollBack();
            
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
        $this->validate($request, [
            'id' => 'required|integer',
            'exam_id' => 'sometimes|integer'
        ]);

        try {
            //获取当前登陆者id
            $id = $request->input('id');
            $examId = $request->input('exam_id', null);

            //获取正在考试中的考试
            $exam = Exam::doingExam($examId);
            Common::valueIsNull($exam, -333);

            //根据id获取考站信息
            $stationTeacher = StationTeacher::where('user_id', $id)->where('exam_id', $exam->id)->first();

            if (is_null($stationTeacher)) {
                throw new \Exception('当前老师没有考试！', 4000);
            }

            $station = $stationTeacher->station;

            //拿到房间
            $room = $this->getRoomId($id, $exam->id);

            //判断其考站或考场是否在该次考试中使用
            $this->checkEffected($exam, $room, $station);

            //将考场名字和考站名字封装起来
            $station->name = $room->name . '-' . $station->name;
            //场次id
            $examScreen=new ExamScreening();
            $roomMsg = $examScreen->getExamingScreening($exam->id);
            $roomMsg_two = $examScreen->getNearestScreening($exam->id);

            if($roomMsg){
                $station->exam_screening_id=$roomMsg->id;
            }elseif($roomMsg_two){
                $station->exam_screening_id=$roomMsg->id;
            }

            //将考场的id封装进去
            $station->room_id = $room->id;

            //将考试的id封装进去
            $station->exam_id = $exam->id;

            //将当前的服务器时间返回
            $station->service_time = time() * 1000;

//            $examinee = new Examinee($exam, ['id' => $id]);
//            $station = $examinee->getStation();

            $station->station_type = $station->type;

            //$redis = Redis::connection('message');
            //$redis->publish('watch_message', json_encode($this->success_data($station)));

            $request['station_id']=$station->id;
            $request['teacher_id']=$id;
            $request['exam_id']=$station->exam_id;
            $this->getExaminee_arr($request);//当前组推送(可以获得)
            $inv=new InvigilatePadController();
            $msg=$inv->getAuthentication_arr($request);//当前考生推送(如果有)
            if($msg) {
                //调用向腕表推送消息的方法
                $examQueue = ExamQueue::where('student_id', '=', $msg->student_id)
                    ->where('station_id', '=', $station->id)
                    ->whereIn('status', [0, 2])
                    ->first();
                if ($examQueue) {
                    $examScreeningStudentData = ExamScreeningStudent::where('exam_screening_id', '=', $examQueue->exam_screening_id)
                        ->where('student_id', '=', $examQueue->student_id)->first();
                    $watchData = Watch::where('id', '=', $examScreeningStudentData->watch_id)->first();
                    $studentWatchController = new StudentWatchController();

                    $request['nfc_code'] = $watchData->nfc_code;
                    $studentWatchController->getStudentExamReminder($request);
                }
            }/*else{
                $request['uid']=;
                $request['room_id']=$id;
                $request['teacher_id']=$id;


                $this->getStation($request);
            }*/

            return response()->json($this->success_data($station));
        } catch (\Exception $ex) {
            return response()->json($this->fail($ex));
        }
    }

    /**
     * 抽签的方法
     * @param $student 学生实例
     * @param $roomId 考试id
     * @param $teacherId 老师id
     * @param $exam 考试实例
     * @return array 返回参数为一个数组
     * @throws \Exception
     * @author Jiangzhiheng
     */
    private function drawlots($student, $roomId, $teacherId, $exam)
    {

        try {
            //获取正在考试中的考试
            $examId = $student->exam_id;

            //得知当前学生是否已经抽签
            $temp = ExamQueue::where('student_id', $student->id)
                ->where('exam_id', $examId)
                ->whereIn('status', [1, 2])
                ->first();
            if (!is_null($temp)) {
                return Station::findOrFail($temp->station_id);
            }

            //判断目前是否应该在这个考场考试
            $this->whetherInthisEntity($student, $examId, $roomId);

            //判断如果是以考场分组
            if (Exam::findOrFail($examId)->sequence_mode == 1) {
                //获取当前小组信息
                list($room_id, $stations) = $this->getRoomIdAndStation($teacherId, $exam);
                //从队列表中通过考场ID得到对应的考生信息
                $examQueue = ExamQueue::examineeByRoomId($room_id, $examId, $stations);
                $studentids = $examQueue->pluck('student_id')->toArray();

                //随机获取一个考站的id
                $ranStationId = $this->ranStationSelect($roomId, $examId, $studentids);

                //将这个值保存在队列表中
                if (!$examQueue = ExamQueue::where('student_id', $student->id)
                    ->where('room_id', $roomId)
                    ->where('exam_id', $examId)
                    ->where('status', 0)
                    ->orderBy('begin_dt', 'asc')
                    ->first()
                ) {
                    throw new \Exception('没有找到考生信息！', 3600);
                };
                if ($examQueue->status != 0) {
                    throw new \Exception('该考生数据错误！', 3650);
                }

                $examQueue->status = 1;
                $examQueue->station_id = $ranStationId;
                if (!$examQueue->save()) {
                    throw new \Exception('抽签失败！请重试！', 3700);
                };
                //将考站的信息返回
                return Station::findOrFail($ranStationId);
            } else {
                //如果是以考站分组，直接按计划好的顺序给出
                //查询该学生当前应该在哪个考站考试
                $examQueue = ExamQueue::where('student_id', $student->id)
                    ->where('exam_id', $examId)
                    ->where('status', 0)
                    ->orderBy('begin_dt', 'asc')
                    ->get();

                if ($examQueue->isEmpty()) {
                    throw new \Exception('该名考生不在计划中！', 3801);
                }

                //获得他应该要去的考站id
                $tempObj = $examQueue->first();
                $stationId = $tempObj->station_id;
                if ($tempObj->status != 0) {
                    throw new \Exception('该考生数据错误！', 3650);
                }

                //获得他应该要去的考场id
                $shouldRoomId = $tempObj->room_id;

                if ($shouldRoomId != $roomId) {
                    throw new \Exception('当前考生走错了考场！请去' . Room::findOrFail($shouldRoomId)->name, 7000);
                }

                //将队列状态变更为1
                $tempObj->status = 1;
                if (!$tempObj->save()) {
                    throw new \Exception('当前抽签失败！', 3901);
                }
                //查出考站的信息
                return Station::findOrFail($stationId);
            }

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 通过教师id和考试实例获取房间的id和拼装好的考站实例数组
     * @param $id
     * @param $exam
     * @return array
     * @throws \Exception
     * @author Jiangzhiheng
     */
    private function getRoomIdAndStation($id, $exam)
    {
        try {
            //获取当前老师的考场对象
            $room = $this->getRoomId($id, $exam->id);

            //获得考场的id
            $room_id = $room->id;
            //获得当前考场考站的实例列表
            $stations = StationTeacher::where('exam_id', $exam->id)->groupBy('station_id')->get();

            $roomStations = [];

            foreach ($stations as $station) {
                $thisStationRoomdId = $station->station->roomStation->room_id;
                $roomStations[$thisStationRoomdId][] = $station;
            }


            return array($room_id, $roomStations[$room_id]);
        } catch (\Exception $ex) {
            throw $ex;
        }
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
            throw new \Exception('当前没有符合条件的队列！', -1000);
        }
        $studentBeginTime = $studentObj->begin_dt;
        $studentEndTime = $studentObj->end_dt;
        if (strtotime($date) > strtotime($studentBeginTime)) {
            $diff = strtotime($date) - strtotime($studentBeginTime);
            $studentObjs = ExamQueue::where('student_id', $uid)->where('status', '<', 2)->get();
            foreach ($studentObjs as $studentObj) {
                $studentObj->begin_dt = date('Y-m-d H:i:s', strtotime($studentBeginTime) + $diff);
                $studentObj->end_dt = date('Y-m-d H:i:s', strtotime($studentEndTime) + $diff);
                \Log::info('drawlots_time', ['begin_dt' => $studentObj->begin_dt, 'end_dt' => $studentObj->end_dt]);
                if (!$studentObj->save()) {
                    throw new \Exception('抽签失败！', -1001);
                }
            }
        }
    }

    /**
     * 判断当前考生是否应该在这个考点考试
     * @param $student 学生实例
     * @param $examId
     * @param $roomId
     * @return bool
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-01
     */
    private function whetherInthisEntity($student, $examId, $roomId)
    {
        try {
            //获得plan表中应该要去哪些考站
            $examPlanStationIds = ExamPlan::where('student_id', '=', $student->id)
                ->where('exam_id', '=', $examId)
                ->orderBy('begin_dt', 'asc')
                ->get()->pluck('room_id');

            //判断当前考站在计划表中的顺序
            $stationIdKey = $examPlanStationIds->search($roomId);
            if ($stationIdKey === false) {
                throw new \Exception('该名考生不在计划中！', 3800);
            }

            $tempExamQueue = ExamQueue::where('student_id', $student->id)
                ->where('exam_id', $examId)
                ->orderBy('begin_dt', 'asc')
                ->get();

            //判断其是否应该在这个考站考试
            $tempStationIdKey = $stationIdKey - 1;
            if ($tempStationIdKey >= 0 && $tempExamQueue[$tempStationIdKey]->status != 3) {
                throw new \Exception('当前考生走错了考场！', 3400);
            }

            return true;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * 根据学生的id数组和房间id，考试id获取随机的考站
     * @param $roomId
     * @param $examId
     * @param $studentids
     * @return mixed
     * @author Jiangzhiheng
     * @time 2016-03-01
     */
    private function ranStationSelect($roomId, $examId, $studentids)
    {
        /*
         * 从ExamQueue表中将房间和学生对应的列表查出
         * 此集合为已经使用了的考站
         */
        $station = ExamQueue::where('room_id', '=', $roomId)
            ->where('exam_id', $examId)
            ->whereIn('student_id', $studentids)
            ->whereNotNull('station_id')
            ->get();

        if (!$station->isEmpty()) {
            $stationIds = $station->pluck('station_id');
        } else {
            $stationIds = collect([]);
        }

        //将其变成一个一维数组
        $stationIdeds = $stationIds->all();

        //为该名考生分配一个还没有选择的station_id
        $stationIds = RoomStation::where('room_id', $roomId)
            ->select(
                'station_id'
            )
            ->get();

        //$stationIds为还没有被使用的考站
        $stationIds = array_diff($stationIds->pluck('station_id')->toArray(), $stationIdeds);
        //$ranStationId为随机选择的一个考站
        $ranStationId = $stationIds[array_rand($stationIds)];
        return $ranStationId;
    }

    /**
     * 判断当前这个考试实体是否在这场考试中被启用
     * @param $exam
     * @param $room
     * @param $station
     * @throws \Exception
     * @author Jiangzhiheng
     * @time 2016-03-04 16:42
     */
    private function checkEffected($exam, $room, $station)
    {
        switch ($exam->sequence_mode) {
            case 1:
                $examFlowRooms = ExamFlowRoom::where('room_id', $room->id)
                    ->where('exam_id', $exam->id)->get();
                $effected = $examFlowRooms->pluck('effected'); //获取effected的一维集合
                if ($effected->search('1') === false) {  //如果集合里面没有1，就报错
                    throw new \Exception('当前老师并没有被安排在这场考试中', -1010);
                }
                break;
            case 2:
                $examFlowStations = ExamFlowStation::where('station_id', $station->id)
                    ->where('exam_id', $exam->id)->get();
                $effected = $examFlowStations->pluck('effected'); //获取effected的一维集合
                if ($effected->search('1') === false) { //如果集合里面没有1，就报错
                    throw new \Exception('当前老师并没有被安排在这场考试中', -1011);
                }
                break;
            default:
                throw new \Exception('系统异常，请重试', -955);
                break;
        }
    }
}