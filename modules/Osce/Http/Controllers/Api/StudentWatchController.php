<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18 0018
 * Time: 9:51
 */

namespace Modules\Osce\Http\Controllers\Api;

use Illuminate\Http\Request;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamDraft;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamResult;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Http\Controllers\CommonController;
use Modules\Osce\Entities\ExamStationStatus;
use Illuminate\Support\Facades\Redis;
use Modules\Osce\Entities\ExamPlan;
use Modules\Osce\Repositories\WatchReminderRepositories;

class StudentWatchController extends CommonController
{

    /**
     * 学生腕表信息
     * @method GET
     * @url /osce/api/student-watch/student-exam-reminder
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     nfc_code    腕表nfc_code
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */
    public function getStudentExamReminder(Request $request, $stationId = null, $examscreeningId = [])
    {
        $this->validate($request, [
            'nfc_code' => 'required|string'
        ]);

        $watchNfcCode = $request->input('nfc_code');
        //根据腕表nfc_code找到腕表
        $watch = Watch::where('code', '=', $watchNfcCode)->first();

        //  根据腕表id找到对应的考试场次和学生
        $watchStudent = ExamScreeningStudent::where('watch_id', '=', $watch->id)
            ->where('is_end', 0)
            ->orderBy('signin_dt', 'desc')
            ->first();
            if(!is_null($watchStudent)){
                //得到学生id
                $studentId = $watchStudent->student_id;
                $examId = Student::find($studentId)->exam_id;
            }else{
                $studentId ='';
                $examId ='';
            }
        
        //调用新的腕表方法
        $watch = new WatchReminderRepositories();
        try{
           $watchStudentData = $watch ->getWatchPublish($examId,$studentId,$watchNfcCode);
            
//            return response()->json(
//                ['nfc_code' => $watchNfcCode, 'data' => $data, 'message' => 'success']
//            );
        }catch (\Exception $ex){
            \Log::alert('刷新腕表调用腕表出错',[$studentId]);
        }

//
//        //根据考生id得到该场考试该阶段的所有队列列表
//        $examQueueModel = new ExamQueue();
//        $examQueueCollect = $examQueueModel->StudentExamQueue($studentId, $examscreeningId);
//
//        if (is_null($examQueueCollect)) {
//            $data['title'] = '未找到学生队列信息';
//            $data['code'] = -4;
//            $redis->publish(md5($_SERVER['HTTP_HOST']) . 'watch_message', json_encode([
//                'nfc_code' => $watchNfcCode,
//                'data' => $data,
//                'message' => 'error'
//            ]));
//            return response()->json(
//                ['nfc_code' => $watchNfcCode, 'data' => $data, 'message' => 'error']
//            );
//        }
//
//        //判断考试的状态
//        $data = $this->nowQueue($examQueueCollect, $stationId);
//        $redis->publish(md5($_SERVER['HTTP_HOST']) . 'watch_message', json_encode([
//            'nfc_code' => $watchNfcCode,
//            'data' => $data,
//            'message' => 'success'
//        ]));
//        return response()->json(
//            $this->success_data($data, 1)
//        );
    }

    /**
     * 学生腕表信息 考试信息判断
     * @param $examQueueCollect
     * @return array
     * @internal param $room_id
     * @author zhouqiang
     */
    public function nowQueue($examQueueCollect, $stationId)
    {

        $statusArray = $examQueueCollect->pluck('status')->toArray();
        if (in_array(1, $statusArray)) {//候考
            return $this->getStatusOneExam($examQueueCollect);
        }

        if (in_array(2, $statusArray)) {//正在考试
            return $this->getStatusTwoExam($examQueueCollect);
        }

        if (in_array(3, $statusArray)) {//结束考试
            return $this->getStatusThreeExam($examQueueCollect, $stationId);
        }

        return $this->getStatusWaitExam($examQueueCollect, $stationId);     //刚绑定腕表
    }

    //判断腕表提醒状态为1时
    private function getStatusOneExam($examQueueCollect)
    {
        $items = array_where($examQueueCollect, function ($key, $value) {
            if ($value->status == 1) {
                return $value;
            }
        });

        $item = array_pop($items);

        if (is_null($item)) {
            throw new \Exception('队列异常');
        }

        $station = $item->station;
        $room = $item->room;
        $data = [
            'code' => 3, // 抽签完成状态（对应界面：请到XX考站）
            'title' => '请进入以下考站考试',
            'roomName' => $room->name . '-' . $station->name,
        ];

        return $data;
    }

    //判断腕表提醒状态为2时
    private function getStatusTwoExam($examQueueCollect)
    {
        $items = array_where($examQueueCollect, function ($key, $value) {
            if ($value->status == 2) {
                return $value;
            }
        });

        $item = array_shift($items);

        if (is_null($item)) {
            throw new \Exception('队列异常');
        }

        $surplus = strtotime($item->end_dt) - time();

        if ($surplus <= 0) {
            //todo 调用jiangzhiheng接口
            //$endStudentExam = ExamQueue::endStudentQueueExam($item->student_id);
        };
        $data = [
            'code' => 4, // 考试状态（对应界面：当前考试剩余时间）
            'title' => '当前考站剩余时间',
            'surplus' => $surplus,
        ];

        return $data;
    }

    //判断腕表提醒状态为3时
    private function getStatusThreeExam($examQueueCollect, $stationId)
    {
        $nextExamQueue = '';
        $examQueue = '';
        foreach ($examQueueCollect as $examQueue) {
            if ($examQueue->status != 3) {
                if (empty($nextExamQueue)) {
                    $nextExamQueue = $examQueue;
                    $time = strtotime($examQueue->begin_dt);
                } else {
                    if ($time >= strtotime($examQueue->begin_dt)) {
                        $nextExamQueue = $examQueue;
                        $time = strtotime($examQueue->begin_dt);
                    }
                }
            }
        }

        if (empty($nextExamQueue)) {
            if (!empty($examQueue)) {
                return $this->getExamComplete($examQueue);
            } else {
                throw new \Exception('没有发现该考生相关排考计划');
            }
        } else {
            //调用状态为0的方法
            $data = $this->getStatusWaitExam($examQueueCollect, $stationId);
        }

        return $data;
    }


    private function getExamComplete($examQueue)
    {
        //查询出学生当前已完成的考试已完成的场次
        $ExamFinishStatus = ExamQueue::where('status', '=', 3)
            ->where('student_id', '=', $examQueue->student_id)
            ->where('student_id', '=', $examQueue->exam_id)
            ->groupBy('exam_screening_id')
            ->select('exam_screening_id')
            ->get()->toArray();

        //获取计划表里所有的场次
        $studentExamScreeningIdArr = ExamPlan::where('exam_id', '=', $examQueue->exam_id)
            ->where('student_id', '=', $examQueue->student_id)->groupBy('exam_screening_id')
            ->select('exam_screening_id')->get()->toArray();

        //查询出考试结果里面所有的场次
        $examResultScreening = ExamResult::where('student_id', '=', $examQueue->student_id)
            ->select('exam_screening_id')->get()->toArray();


            //  todo 这里判断有待完善，zhouqiang 2016-4-30；


        if (count($ExamFinishStatus) >= count($studentExamScreeningIdArr)) {

            //判断已考完的场次是否和队列里考完的场次相等
            if (count($examResultScreening) >= count($ExamFinishStatus)) {
                $testresultModel = new TestResult();
                $score = $testresultModel->AcquireExam($examQueue->student_id, $examResultScreening);
                $data = [
                    'code' => 6, // 考试结束全部考完（对应界面：显示考试完成，显示总成绩）
                    'title' => '考试完成，最终总成绩',
                    'score' => $score,
                ];

                return $data;
            } else {
                $data = [
                    'code' => -1,
                    'title' => '当前考试已完成',
                ];

                return $data;
            }

        } else {
            $data = [
                'code' => -1,
                'title' => '还有考试未完成',
            ];

            return $data;
        }
    }

    //判断腕表提醒状态为0时
    private function getStatusWaitExam($examQueueCollect, $stationId)
    {
        $items = array_where($examQueueCollect, function ($key, $value) {
            if ($value->status == 0) {
                return $value;
            }
        });

        $item = array_shift($items);
        // 判断老师是否准备完成

        if (empty($this->getTeacherPrepareStatus($item))) {

            $data = $this->getStudentWaitMessage($item);


        } else {
            $data = $this->getTeacherPrepareStatus($item);
        }
        return $data;
    }

    private function getWillStudent($item)
    {
        $studentNum = 0;
        if(is_null($item->station_id)){

            $willStudents = ExamQueue::where('room_id', '=', $item->room_id)
                ->where('exam_screening_id', '=', $item->exam_screening_id)
                ->where('status', '=', 0)
                ->orderBy('begin_dt', 'asc')
                ->get();
        }else{
            $willStudents = ExamQueue::where('room_id', '=', $item->room_id)
                ->where('exam_screening_id', '=', $item->exam_screening_id)
                ->where('station_id', '=', $item->station_id)
                ->where('status', '=', 0)
                ->orderBy('begin_dt', 'asc')
                ->get();
        }


        foreach ($willStudents as $key => $willStudent) {
            if ($willStudent->student_id == $item->student_id) {
                $studentNum = $key;
                continue;
            }
        }

        return $studentNum;
    }

    /**
     * 根据腕表code得到nfc_code
     * @method GET
     * @url /osce/api/student-watch/watch-nfc
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     code       (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     *
     */
    public function getWatchNfc(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);
        $code = $request->get('code');
        $watchNfc = Watch::where('nfc_code', '=', $code)->first();
        if ($watchNfc) {
            $data = [
                'nfc_code' => $watchNfc->code,
            ];
            return response()->json(
                $this->success_data($data, 1)
            );
        } else {
            $data = [
                'nfc_code' => '',
            ];
            return response()->json(
                $this->success_data($data, -2, '没有找到对应的nfc_code')
            );
        }
    }








    private function getTeacherPrepareStatus($item)
    {

        $examStationStatusModel = new ExamStationStatus();
        //判断考试的排考模式
        $data = [];
        if (is_null($item->station_id)) {

            //先拿到该考场里所有的考站id
            $stationId = $this->getStationData($item);
            //考场模式就看该场考试考场老师是否都准备完成
            $instance = $examStationStatusModel->where('exam_id', '=', $item->exam_id)
                ->where('exam_screening_id', '=', $item->exam_screening_id)
                ->where('status', '=', 0)
                ->whereIn('station_id', $stationId)
                ->first();
            if ($instance) {
                $data = [
                    'code' => 0, // 0，等待状态（对应界面：Prepare_fragment）
                    'title' => '该考场还有老师在准备中',
                    'willStudents' => '',
                ];
            }

        } else {
            //考站模式就看该考站里的老师是否准备完成
            $instance = $examStationStatusModel->where('exam_id', '=', $item->exam_id)
                ->where('exam_screening_id', '=', $item->exam_screening_id)
                ->where('station_id', '=', $item->station_id)
                ->first();
            if ($instance->status == 0) {
                $data = [
                    'code' => 0, // 0，等待状态（对应界面：Prepare_fragment）
                    'title' => '等待老师准备中',
                    'willStudents' => '',
                ];
            }

        }

        return $data;
    }


    private function getStudentWaitMessage($item)
    {


        //判断前面是否有人考试,分考场和考站模式
        if (empty($item->station_id)) {

           $stationId =  $this->getStationData($item);
            $examStudent = ExamQueue::where('room_id', '=', $item->room_id)
                ->where('exam_id', '=', $item->exam_id)
                ->whereIn('station_id', $stationId)
                ->where('status','=',3)
                ->count();
        } else {
            $examStudent = ExamQueue::where('room_id', '=', $item->room_id)
                ->where('exam_id', '=', $item->exam_id)
                ->where('station_id', '=', $item->station_id)
                ->whereBetween('status', [1, 2])
                ->count();
        }
        //判断前面等待人数
        $studentnum = $this->getwillStudent($item);
        if ($examStudent == 0) {
            $willStudents = $studentnum + 1;
        } else {
            $willStudents = $studentnum;
        }

        //判断预计考试时间
        $examtimes = date('H:i', (strtotime($item->begin_dt)));

        //判断进入如的考场教室名字
        $examRoomName = $item->room->name;
        if ($willStudents > 0) {
            $data = [
                'code' => 1, // 侯考状态（对应界面：前面还有多少考生，估计等待时间）
                'title' => '考生等待信息',
                'willStudents' => $willStudents,
                'estTime' => $examtimes,
                'willRoomName' => $examRoomName,

            ];
        } else {
            if (is_null($item->station_id)) {
                $data = [
                    'code' => 2, // 引导状态（对应界面：请前往教室420）
                    'title' => '请进入以下考场考试',
                    'willStudents' => '',
                    'estTime' => '',
                    'willRoomName' => '',
                    'roomName' => $examRoomName,
                ];
            } else {
                $data = [
                    'code' => 2, // // 引导状态（对应界面：请前往教室420）
                    'title' => '请进入以下考站考试',
                    'willStudents' => '',
                    'estTime' => '',
                    'willRoomName' => '',
                    'roomName' => $examRoomName . '-' . $item->station->name,
                ];
            }

        }

        return $data;
    }

    //获取考场下所有的考站
    private function getStationData($item)
    {
        //先拿到该考场里所有的考站id
        $stationId = ExamDraft::  leftJoin('exam_draft_flow', 'exam_draft_flow.id', '=', 'exam_draft.exam_draft_flow_id')
            ->where('exam_draft.room_id', '=', $item->room_id)
            ->where('exam_draft_flow.exam_id', '=', $item->exam_id)
            ->get()->pluck('station_id');
        return $stationId;
    }



    private  function getStudentWatchNfcCode($studentId)
    {
        //拿到正在进行的考试
        $nfc_Code = '';
        $exam = Exam::doingExam();
        $examScreeningModel = new ExamScreening();
        $examScreening = $examScreeningModel->getExamingScreening($exam->id);
        if (is_null($examScreening)) {
            $examScreening = $examScreeningModel->getNearestScreening($exam->id);
        }
        $exam_screen_id = $examScreening->id;

        $examScreeningStudentData = ExamScreeningStudent::where('exam_screening_id',$exam_screen_id)
            ->where('student_id', '=', $studentId)->first();
        if (!is_null($examScreeningStudentData)) {
            //拿到腕表的nfc_code
            $watchData = Watch::where('id', '=', $examScreeningStudentData->watch_id)->first();
            $nfc_Code = $watchData->code;
        }
        return $nfc_Code;
    }

}