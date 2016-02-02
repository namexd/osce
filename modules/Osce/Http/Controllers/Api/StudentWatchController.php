<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/1/18 0018
 * Time: 9:51
 */

namespace Modules\Osce\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Input;
use Modules\Osce\Entities\Exam;
use Modules\Osce\Entities\ExamFlow;
use Modules\Osce\Entities\ExamQueue;
use Modules\Osce\Entities\ExamScore;
use Modules\Osce\Entities\ExamScreening;
use Modules\Osce\Entities\ExamScreeningStudent;
use Modules\Osce\Entities\Standard;
use Modules\Osce\Entities\Station;
use Modules\Osce\Entities\StationVcr;
use Modules\Osce\Entities\Student;
use Modules\Osce\Entities\TestAttach;
use Modules\Osce\Entities\Teacher;
use Modules\Osce\Entities\TestResult;
use Modules\Osce\Entities\Watch;
use Modules\Osce\Entities\WatchLog;
use Modules\Osce\Http\Controllers\CommonController;
use DB;
use Storage;

class StudentWatchController extends CommonController
{

    /**
     * 学生腕表信息
     * @method GET
     * @url /osce/api/student-watch/student-exam-reminder
     * @access public
     * @param Request $request get请求<br><br>
     * <b>get请求字段：</b>
     * * string     watch_id    腕表 id   (必须的)
     *
     * @return json
     *
     * @version 1.0
     * @author zhouqiang <zhouqiang@misrobot.com>
     * @date
     * @copyright 2013-2015 MIS misrobot.com Inc. All Rights Reserved
     */

    public function   getStudentExamReminder(Request $request)
    {
        $this->validate($request, [
            'code' => 'required'
        ]);
        $data = [
            'title' => '',
            'willStudents' => '',
            'estTime' => '',
            'willRoomName' => '',
            'roomName' => '',
            'nextExamName' => '',
            'surplus' => '',
            'score' => '',
            ];
        $code =0;
        $watchCode = $request->input('code');

        //根据设备编号找到设备id
        $watchId= Watch::where('code','=',$watchCode)->select('id')->first();
        //  根据腕表id找到对应的考试场次和学生

        $watchStudent = ExamScreeningStudent::where('watch_id','=',$watchId->id)->select('student_id','exam_screening_id')->first();
        if (!$watchStudent) {
            $data['title'] = '没有找到学生的腕表信息';
            return response()->json(
                $this->success_data($data, $code)
            );
        }
        //得到场次id
//        $examScreeningId= $watchStudent->exam_screening_id;
        //得到学生id
        $studentId = $watchStudent->student_id;
       // 根据考生id找到当前的考试
        $examInfo = Student::where('id', '=', $studentId)->select('exam_id')->first();
        $examId = $examInfo->exam_id;
        //根据考生id在队列中得到当前考试的所有考试队列
        $ExamQueueModel = new ExamQueue();
        $examQueueCollect = $ExamQueueModel->StudentExamQueue($studentId);
        dump($examQueueCollect);
         //判断考试的状态
        $nowNextQueue = $ExamQueueModel->nowQueue($examQueueCollect);
        $nowQueue = $nowNextQueue[0];
        $nextQueue = $nowNextQueue[1];
        $nowTime = time();
        if (empty($nowQueue)) {
            //查询出学生对应的考试的所有流程
            $ExamFlowModel = new  ExamFlow();
            $studentExamSum = $ExamFlowModel->studentExamSum($examId);

            //学生完成的考试
            $ExamFinishStatus = ExamQueue::where('status', '=', 3)->where('student_id', '=', $studentId)->count();

//            if ($ExamFinishStatus == 0) {
//                $data['title'] = '你目前没有考试';
//            }
            if ($ExamFinishStatus < $studentExamSum) {
                $data['title'] = '你还有考试还未完成，请到候考区候考';
                $code=1;
            } else {

                $TestResultModel = new TestResult();
                $ExamResult = $TestResultModel->AcquireExam($studentId);
                if (!empty($ExamResult)) {
                    $studentExamScore = $ExamResult;
                    $data['score'] = $studentExamScore;
                    $data['title'] = '考试完成 最终成绩';
                    $code=6;
                } else {

                    return response()->json(
                        $this->fail(new \Exception('成绩推送失败'))
                    );
                }
            }

        } else {
            if ($nowQueue->status == 1) {
                if (strtotime($nowQueue->begin_dt) - $nowTime <= 120 && strtotime($nowQueue->begin_dt) - $nowTime > 0) {
                    $examRoomName = $nowQueue->room_name;
                    $data['roomName'] = $examRoomName;
                    $data['title'] = '考生开考通知';
                    $code=2;

                } else {
                    $willStudents = ExamQueue::where('room_id', '=', $nowQueue['room_id'])
                        ->whereBetween('status', [1, 2])
                        ->count();
                    $examtimes = strtotime($nowQueue->begin_dt);
                    $examRoomName = $nowQueue->room_name;
                    $data['title'] = '考生等待信息';
                    $data['willStudents'] = $willStudents;
                    $data['estTime'] = $examtimes;
                    $data['willRoomName'] = $examRoomName;
                    $code = 1;
                }

            } else {
                $surplus = ((strtotime($nowQueue['begin_dt']) + ($nowQueue->mins * 60)) - $nowTime);
                if ($surplus <= 0) {
                    if (!empty($nextQueue)) {
                        $nextExamName = $nextQueue ['room_name'];
                        $data['nextExamName'] = $nextExamName;
                        $data['title'] = '下一场';
                        $code=5;
                    } else {
                        $data['title'] = '目前没有下一场，请等待下一步通知';
                    }
                } else {
                    $surplus = floor($surplus / 60) . ':' . $surplus % 60;
                    $data['surplus'] = $surplus;
                    $data['title'] = '当前考站剩余时间';
                    $code =4;
                }
            }
        }
          return response()->json(
            $this->success_data($data ,$code)
        );
    }


}